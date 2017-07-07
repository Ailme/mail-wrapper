<?php

namespace app\modules\user;

use Yii;
use yii\base\Object;
use app\modules\user\models\User;

/**
 * Class Mailer
 *
 * @package app\modules\user
 */
class Mailer extends Object
{
    public $viewPath = '@app/modules/user/mails';
    public $sender;
    public $module;

    /**
     * @param User $user
     *
     * @return bool
     */
    public function sendConfirmEmailMessage(User $user)
    {
        return $this->sendMessage(
            $user->email,
            Module::t('module', 'EMAIL_CONFIRMATION_FOR') . Yii::$app->name,
            'emailConfirm',
            ['user' => $user]
        );
    }

    /**
     * @param $email
     * @param User $user
     *
     * @return bool
     */
    public function sendPasswordResetToken($email, User $user)
    {
        return $this->sendMessage(
            $email,
            Module::t('module', 'PASSWORD_RESET_FOR') . Yii::$app->name,
            'passwordResetToken',
            ['user' => $user]
        );
    }

    /**
     * @param $to
     * @param $subject
     * @param $view
     * @param array $params
     *
     * @return bool
     */
    protected function sendMessage($to, $subject, $view, $params = [])
    {
        /** @var \yii\mail\BaseMailer $mailer */
        $mailer = Yii::$app->mailer;

        if ($this->sender === null) {
            $this->sender = Yii::$app->params['supportEmail'];
        }

        $mailer->compose(['html' => $this->viewPath . DS . $view], $params)
            ->setTo($to)
            ->setFrom([$this->sender => Yii::$app->name])
            ->setSubject($subject);

        Yii::$app->rabbitmq->sendMail('send.mail', [
            'subject' => $subject,
            'email'   => $to,
            'type'    => 'text/html',
            'body'    => $mailer->render($this->viewPath . DS . $view, $params),
        ]);

        return true;
//        return $mailer->send();
    }
}
