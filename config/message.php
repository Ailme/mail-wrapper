<?php
/**
 * use:
 * php yii message @app/config/message.php
 */
return [
    'sourcePath' => dirname(__DIR__),
    'languages' => ['ru', 'en'],
    'translator' => 'Yii::t',
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
    'messagePath' => dirname(__DIR__) . '/messages',
    'overwrite' => true,
];
