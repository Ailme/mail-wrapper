<?php

namespace app\modules\user;

use Yii;
use yii\console\Application as ConsoleApplication;
use app\modules\user\Mailer;

/**
 * user module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @var \app\modules\user\Mailer
     */
    public $mailer;

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\user\controllers';

    /**
     * @var int
     */
    public $passwordResetTokenExpire = 3600;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->mailer = Yii::createObject([
            'class' => 'app\modules\user\Mailer',
            'module' => $this,
        ]);

        parent::init();

        if (Yii::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'app\modules\user\commands';
        }
    }

    /**
     * @param $category
     * @param $message
     * @param array $params
     * @param null $language
     *
     * @return string
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('modules/user/' . $category, $message, $params, $language);
    }
}
