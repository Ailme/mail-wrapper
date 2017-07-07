<?php

use app\modules\mail\models\Mail;
use app\modules\mail\Module;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \app\modules\mail\forms\MailSearchForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mail-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-xs-12 col-sm-1">
            <?= $form->field($model, 'id') ?>
        </div>

        <div class="col-xs-12 col-sm-1">
            <?= $form->field($model, 'type')->dropDownList(Mail::getTypesArray(), ['prompt'=>'-']) ?>
        </div>

        <div class="col-xs-12 col-sm-2">
            <?= $form->field($model, 'date_from')->input('date') ?>
        </div>

        <div class="col-xs-12 col-sm-2">
            <?= $form->field($model, 'date_to')->input('date') ?>
        </div>

        <div class="col-xs-12 col-sm-2">
            <?= $form->field($model, 'from') ?>
        </div>

        <div class="col-xs-12 col-sm-2">
            <?= $form->field($model, 'to') ?>
        </div>

        <div class="col-xs-12 col-sm-2">
            <?= $form->field($model, 'subject') ?>
        </div>

        <div class="col-xs-12 col-sm-2">
            <?= $form->field($model, 'text') ?>
        </div>

        <?php /*
        <div class="col-sm-1">
            <?= $form->field($model, 'files')->dropDownList(['yes' => Module::t('module', 'EXIST'), 'no' => Module::t('module', 'NO')], ['prompt' => '-']) ?>
        </div>
 */?>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'BUTTON_SEARCH'), ['class' => 'btn btn-primary', 'name' => 'filter', 'value' => '1']) ?>
        <?= Html::submitButton(Yii::t('app', 'BUTTON_RESET'), ['class' => 'btn btn-default', 'name' => 'reset', 'value' => '1']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
