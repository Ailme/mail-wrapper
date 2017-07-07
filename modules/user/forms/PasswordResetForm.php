<?php

namespace app\modules\user\forms;

use app\modules\user\models\User;
use app\modules\user\Module;
use Yii;
use yii\base\Model;
use yii\base\InvalidParamException;

/**
 * Password reset form
 */
class PasswordResetForm extends Model
{
    public $password;

    /**
     * @var User
     */
    private $_user;

    /**
     * Creates a form model given a token.
     *
     * @param string $token
     * @param $timeout
     * @param array $config name-value pairs that will be used to initialize the object properties
     *
     */
    public function __construct($token, $timeout, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidParamException(Module::t('module', 'PASSWORD_RESET_TOKEN_CANNOT_BE_BLANK'));
        }
        $this->_user = User::findByPasswordResetToken($token, $timeout);
        if (!$this->_user) {
            throw new InvalidParamException(Module::t('module', 'WRONG_PASSWORD_RESET_TOKEN'));
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * Resets password.
     *
     * @return bool if password was reset.
     */
    public function resetPassword()
    {
        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removePasswordResetToken();

        return $user->save(false);
    }
}
