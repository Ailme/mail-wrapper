<?php

namespace app\modules\admin\models;

use app\modules\admin\Module;
use app\modules\user\models\User as BaseModel;
use yii\helpers\ArrayHelper;


/**
 * Class User
 *
 * @package app\modules\admin\models
 */
class User extends BaseModel
{
    const SCENARIO_ADMIN_CREATE = 'adminCreate';
    const SCENARIO_ADMIN_UPDATE = 'adminUpdate';

    public $newPassword;
    public $newPasswordRepeat;

    /**
     * @return array
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['newPassword'], 'required', 'on' => self::SCENARIO_ADMIN_CREATE],
        ]);
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_ADMIN_CREATE] = ['username', 'email', 'status', 'newPassword'];
        $scenarios[self::SCENARIO_ADMIN_UPDATE] = ['username', 'email', 'status', 'newPassword'];

        return $scenarios;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'newPassword' => Module::t('module', 'NEW_PASSWORD'),
        ]);
    }

    /**
     * @param bool $insert
     *
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (!empty($this->newPassword)) {
                $this->setPassword($this->newPassword);
            }

            return true;
        }

        return false;
    }
}
