<?php

namespace app\modules\user\controllers;

use app\modules\user\forms\EmailConfirmForm;
use app\modules\user\forms\LoginForm;
use app\modules\user\forms\PasswordResetForm;
use app\modules\user\forms\PasswordResetRequestForm;
use app\modules\user\forms\SignupForm;
use app\modules\user\Module;
use Yii;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

/**
 * Default controller for the `user` module
 */
class DefaultController extends Controller
{
    /**
     * @var \app\modules\user\Module
     */
    public $module;

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @return \yii\web\Response
     */
    public function actionIndex()
    {
        return $this->redirect(['profile/index'], 301);
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if (($user = $model->signup())) {
                Yii::$app->getSession()->setFlash('success', Module::t('module', 'FLASH_EMAIL_CONFIRM_REQUEST'));

                return $this->goHome();
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * @param $token
     *
     * @return \yii\web\Response
     * @throws BadRequestHttpException
     */
    public function actionEmailConfirm($token)
    {
        try {
            $model = new EmailConfirmForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->confirmEmail()) {
            Yii::$app->getSession()->setFlash('success', Module::t('module', 'FLASH_EMAIL_CONFIRM_SUCCESS'));
        } else {
            Yii::$app->getSession()->setFlash('error', Module::t('module', 'FLASH_EMAIL_CONFIRM_ERROR'));
        }

        return $this->goHome();
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionPasswordResetRequest()
    {
        $model = new PasswordResetRequestForm($this->module->passwordResetTokenExpire);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', Module::t('module', 'FLASH_PASSWORD_RESET_REQUEST'));
                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', Module::t('module', 'FLASH_PASSWORD_RESET_ERROR'));
            }
        }
        return $this->render('passwordResetRequestToken', [
            'model' => $model,
        ]);
    }

    /**
     * @param $token
     *
     * @return string|\yii\web\Response
     * @throws BadRequestHttpException
     */
    public function actionPasswordReset($token)
    {
        try {
            $model = new PasswordResetForm($token, $this->module->passwordResetTokenExpire);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('success', Module::t('module', 'FLASH_PASSWORD_RESET_SUCCESS'));

            return $this->goHome();
        }

        return $this->render('passwordReset', [
            'model' => $model,
        ]);
    }
}
