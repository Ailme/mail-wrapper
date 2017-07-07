<?php

namespace tests\_fixtures;

use yii\test\ActiveFixture;

/**
 * Class UserFixture
 *
 * @package tests\_fixtures
 */
class UserFixture extends ActiveFixture
{
    public $modelClass = 'app\modules\user\models\User';
    public $dataFile = '@tests/_fixtures/data/user.php';
}