<?php
/**
 * use:
 * php yii message @app/modules/user/config/message.php
 */
return [
    'sourcePath' => '@app/modules/mail',
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
    'messagePath' => '@app/modules/mail/messages',
    'overwrite' => true,
];
