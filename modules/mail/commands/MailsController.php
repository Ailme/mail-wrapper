<?php

namespace app\modules\mail\commands;

use Yii;
use yii\console\Controller;

/**
 * Class MailsController
 *
 * @package app\modules\mail\commands
 */
class MailsController extends Controller
{
    public function actionIndex()
    {
        echo 'yii mail/mails/read' . PHP_EOL;
    }

    /**
     * @return int
     */
    public function actionRead()
    {
        $stdin = fopen('php://stdin', 'r');
        $contents = stream_get_contents($stdin);
        fclose($stdin);

        /** @var \PhpMimeMailParser\Parser $Parser */
        $Parser = Yii::$app->mailParser;
        $Parser->setText($contents);

        /** @var \app\modules\mail\services\MailService $mailService */
        $mailService = Yii::$container->get('mailService');
        /** @var \app\modules\mail\services\ElasticMailService $elasticService */
        $elasticService = Yii::$container->get('elasticService');

        try {
            $mail = $mailService->insertMail($this->parser);
            $mailService->addToSd($this->parser, $mail);

            $elasticService->insert($this->parser, $mail);
        } catch (\Exception $e) {
            \Yii::error([
                'msg' => $e->getMessage(),
            ], 'console');

            return self::EXIT_CODE_ERROR;
        }

        return self::EXIT_CODE_NORMAL;
    }

    /**
     * удаление старых писем
     */
    public function actionDeleteOld()
    {
        $days = 14;

        /** @var \app\modules\mail\services\MailService $mailService */
        $mailService = Yii::$container->get('mailService');
        $mailService->deleteOld($days);
    }
}
