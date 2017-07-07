<?php

namespace app\modules\mail;

use yii\base\BootstrapInterface;

/**
 * Class Bootstrap
 *
 * @package app\modules\mail
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        $app->i18n->translations['modules/mail/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'forceTranslation' => true,
            'basePath' => '@app/modules/mail/messages',
            'fileMap' => [
                'modules/mail/module' => 'module.php',
            ],
        ];
    }
}
