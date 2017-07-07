<?php
/**
 * use:
 * php yii message @app/modules/admin/config/message.php
 */
return [
    'sourcePath' => '@app/modules/admin',
    'languages' => ['ru', 'en'],
    'translator' => 'Module::t',
    'sort' => true,
    'removeUnused' => true,
    'only' => ['*.php'],
    'except' => [
        '.git',
        '.gitignore',
        '.gitkeep',
        '/messages',
        '/vendor',
    ],
    'format' => 'php',
    'messagePath' => '@app/modules/admin/messages',
    'overwrite' => true,
];
