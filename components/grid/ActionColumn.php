<?php

namespace app\components\grid;

/**
 * Class ActionColumn
 * @package app\components\grid
 */
class ActionColumn extends \yii\grid\ActionColumn
{
    public $contentOptions = [
        'class' => 'action-column',
    ];
}