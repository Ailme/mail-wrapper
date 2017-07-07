<?php

namespace app\modules\user\commands;

use app\modules\user\models\User;
use app\modules\user\Module;
use Yii;
use yii\base\Model;
use yii\console\Controller;
use yii\console\Exception;
use yii\helpers\Console;

/**
 * Class UsersController
 *
 * @package app\modules\user\commands
 */
class UsersController extends Controller
{
    public function actionIndex()
    {
        echo 'yii user/users/create' . PHP_EOL;
        echo 'yii user/users/remove' . PHP_EOL;
        echo 'yii user/users/activate' . PHP_EOL;
        echo 'yii user/users/change-password' . PHP_EOL;
    }

    public function actionCreate()
    {
        $model = new User();
        $this->readValue($model, 'username');
        $this->readValue($model, 'email');
        $model->setPassword($this->prompt(Module::t('module', 'PASSWORD:'), [
            'required' => true,
            'pattern' => '#^.{6,255}$#i',
            'error' => Yii::t('app', 'More than 6 symbols'),
        ]));
        $model->generateAuthKey();
        $this->log($model->save());
    }

    public function actionRemove()
    {
        $username = $this->prompt(Module::t('module', 'USERNAME:'), ['required' => true]);
        $model = $this->findModel($username);
        $this->log($model->delete());
    }

    public function actionActivate()
    {
        $username = $this->prompt(Module::t('module', 'USERNAME:'), ['required' => true]);
        $model = $this->findModel($username);
        $model->status = User::STATUS_ACTIVE;
        $model->removeEmailConfirmToken();
        $this->log($model->save());
    }

    public function actionChangePassword()
    {
        $username = $this->prompt(Module::t('module', 'USERNAME:'), ['required' => true]);
        $model = $this->findModel($username);
        $model->setPassword($this->prompt(Module::t('module', 'NEW_PASSWORD:'), [
            'required' => true,
            'pattern' => '#^.{6,255}$#i',
            'error' => Yii::t('app', 'More than 6 symbols'),
        ]));
        $this->log($model->save());
    }

    /**
     * @param string $email
     *
     * @throws \yii\console\Exception
     * @return User the loaded model
     */
    private function findModel($email)
    {
        if (!$model = User::findOne(['email' => $email])) {
            throw new Exception(Module::t('module', 'USER_NOT_FOUND'));
        }

        return $model;
    }

    /**
     * @param Model $model
     * @param string $attribute
     */
    private function readValue($model, $attribute)
    {
        $model->$attribute = $this->prompt(mb_convert_case($attribute, MB_CASE_TITLE, 'utf-8') . ':', [
            'validator' => function ($input, &$error) use ($model, $attribute) {
                $model->$attribute = $input;
                if ($model->validate([$attribute])) {
                    return true;
                } else {
                    $error = implode(',', $model->getErrors($attribute));

                    return false;
                }
            },
        ]);
    }

    /**
     * @param bool $success
     */
    private function log($success)
    {
        if ($success) {
            $this->stdout(Yii::t('app', 'SUCCESS!'), Console::FG_GREEN, Console::BOLD);
        } else {
            $this->stderr(Yii::t('app', 'ERROR!'), Console::FG_RED, Console::BOLD);
        }
        echo PHP_EOL;
    }
}
