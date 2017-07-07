<?php

namespace app\modules\mail\services;

use app\modules\mail\models\Mail;
use app\modules\mail\models\SDExchangeMail;
use app\modules\mail\Module;
use PhpMimeMailParser\Attachment;
use PhpMimeMailParser\Parser;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class MailService
 *
 * @package app\modules\mail\services
 */
class MailService
{
    /**
     * Сохраняем письмо в БД
     *
     * @param Parser $Parser
     *
     * @return Mail
     */
    public function insertMail(Parser $Parser)
    {
        $model = new Mail();
        $model->setTypeIncoming();
        $model->created_at = date(DATE_ATOM_1);
        $model->from = ''; //implode(', ', array_column($Parser->getAddresses('from'), 'address'));
        $model->to = ''; //implode(', ', array_column($Parser->getAddresses('to'), 'address'));
        $model->subject = ''; //(string)$Parser->getHeader('subject');
        $model->files = ''; //implode(', ', $files);
        $model->html = ''; //(string)$Parser->getMessageBody('html');
        $model->text = ''; //(string)$Parser->getMessageBody('text');
        $model->xml = '';
        $model->raw = $Parser->getData();
        $model->save();

        return $model;
    }

    /**
     * Сохраняем данные в SD, если письмо относится к интеграции
     *
     * @param Parser $Parser
     * @param Mail $mail
     */
    public function addToSd(Parser $Parser, Mail $mail)
    {
        $attachments = $Parser->getAttachments(true);

        $files = [];
        foreach ($attachments as $attachment) {
            $files[] = $attachment->getFilename();
        }

        $allowedEmails = [
            'allowed-mail@local.dev',
        ];

        $blackEmails = [
            'bad-mail@local.dev',
        ];

        $toList = array_column($Parser->getAddresses('to'), 'address');
        $toList = array_map('trim', $toList);
        $toList = array_map('strtolower', $toList);
        $toList = array_filter($toList, function ($email) use ($allowedEmails) {
            return in_array($email, $allowedEmails);
        });

        $fromList = array_column($Parser->getAddresses('from'), 'address');
        $fromList = array_map('trim', $fromList);
        $fromList = array_map('strtolower', $fromList);
        $fromList = array_filter($fromList, function ($email) use ($blackEmails) {
            return !in_array($email, $blackEmails);
        });

        foreach ($toList as $to) {
            foreach ($fromList as $from) {
                $sdMail = new SDExchangeMail();
                $sdMail->created_at = $mail->created_at;
                $sdMail->mailbox = $to;
                $sdMail->raw = $Parser->getData();
                $sdMail->from = $from;
                $sdMail->subject = (string)$Parser->getHeader('subject');
                $sdMail->textPlain = (string)$Parser->getMessageBody('html');
                $sdMail->textHtml = (string)$Parser->getMessageBody('text');
                $sdMail->files = implode(', ', $files);
                $sdMail->save();
            }
        }
    }

    /**
     * @param Mail $mail
     */
    public function addToSdWithoutParser(Mail $mail)
    {
        /** @var \PhpMimeMailParser\Parser $Parser */
        $Parser = Yii::$app->mailParser;
        $Parser->setText($mail->raw);

        $this->addToSd($Parser, $mail);
    }

    /**
     * @param int $days
     *
     * @return int
     */
    public function deleteOld($days = 14)
    {
        return Mail::deleteAll(['<=', 'DATE(created_at)', date(DATE_ATOM_SHORT, strtotime("-$days days"))]);
    }

    /**
     * @param $name
     *
     * @param Mail $mail
     *
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function downloadFile($name, Mail $mail)
    {
        /** @var \PhpMimeMailParser\Parser $Parser */
        $Parser = Yii::$app->mailParser;
        $Parser->setText($mail->raw);
        $attachments = $Parser->getAttachments(true);

        /** @var Attachment $file */
        $file = null;
        foreach ($attachments as $attachment) {
            /** @var Attachment $attachment */
            if ($attachment->getFilename() === $name) {
                $file = $attachment;
                break;
            }
        }

        if (!$file) {
            throw new NotFoundHttpException(Module::t('module', 'REQUESTED_FILE_NOT_FOUND'));
        }

        /** @var resource $fp */
        if (($fp = fopen('php://output', 'w'))) {

            if (ob_get_level()) {
                ob_end_clean();
            }

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header("Content-Disposition: attachment; filename='{$file->getFilename()}'");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');

            while (($bytes = $file->read())) {
                fwrite($fp, $bytes);
            }
            fclose($fp);
        } else {
            throw new \Exception('Could not write attachments. Your directory may be unwritable by PHP.');
        }
    }

    /**
     * @param Mail $mail
     *
     * @return Response
     */
    public function download(Mail $mail)
    {
        $name = $mail->id . '-' . $mail->subject . '.eml';

        /** @var Response $response */
        $response = Yii::$app->response;

        return $response->sendContentAsFile($mail->raw, $name);
    }
}