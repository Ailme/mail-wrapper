<?php
/**
 * use:
 * php yii message @app/modules/main/config/message.php
 */
return [
    'sourcePath' => '@app/modules/main',
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
    'messagePath' => '@app/modules/main/messages',
    'overwrite' => true,
];
