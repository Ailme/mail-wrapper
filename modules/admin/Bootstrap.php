<?php

namespace app\modules\admin;

use yii\base\BootstrapInterface;

/**
 * Class Bootstrap
 *
 * @package app\modules\admin
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        $app->i18n->translations['modules/admin/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'forceTranslation' => true,
            'basePath' => '@app/modules/admin/messages',
            'fileMap' => [
                'modules/admin/module' => 'module.php',
            ],
        ];
    }
}
