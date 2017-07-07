<?php
use app\modules\user\Module;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user app\modules\user\models\User */

$confirmLink = Yii::$app->urlManager->createAbsoluteUrl([
    'user/default/email-confirm',
    'token' => $user->email_confirm_token,
]);
?>
<div class="email-confirm">

    <p><?= Yii::t('app', 'HELLO') ?>, <?= Html::encode($user->username) ?>!</p>

    <p><?= Module::t('module', 'FOLLOW_TO_CONFIRM_EMAIL') ?></p>

    <p><?= Html::a(Html::encode($confirmLink), $confirmLink) ?></p>

    <p><?= Module::t('module', 'IGNORE_IF_DO_NOT_REGISTER') ?></p>

</div>
