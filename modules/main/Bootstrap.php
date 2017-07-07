<?php

namespace app\modules\main;

use yii\base\BootstrapInterface;

/**
 * Class Bootstrap
 *
 * @package app\modules\main
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        $app->i18n->translations['modules/main/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'forceTranslation' => true,
            'basePath' => '@app/modules/main/messages',
            'fileMap' => [
                'modules/main/module' => 'module.php',
            ],
        ];
    }
}
