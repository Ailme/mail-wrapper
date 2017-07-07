<?php
use app\modules\user\Module;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user app\modules\user\models\User */
$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['user/default/password-reset', 'token' => $user->password_reset_token]);
?>
<div class="password-reset">
    <p><?= Yii::t('app', 'HELLO')?>, <?= Html::encode($user->username) ?>!</p>

    <p><?= Module::t('module', 'FOLLOW_TO_RESET_PASSWORD') ?></p>

    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
</div>
