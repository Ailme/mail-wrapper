<?php

use app\components\grid\LinkColumn;
use app\modules\mail\models\Mail;
use app\modules\mail\Module;
use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel \app\modules\mail\forms\MailSearchForm */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('module', 'TITLE_MAILS');

$script = <<<JS
$(document).ready(function() {
    setInterval(function(){ document.location.reload() }, 120000);
});
JS;
$this->registerJs($script);
?>
<div class="mail-index">

    <?php Pjax::begin(); ?>

    <?= $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{pager}\n{summary}\n{items}\n{pager}",
        'tableOptions' => [
            'class' => 'table table-bordered',
        ],
        'rowOptions' => function ($model, $key, $index, $grid) {
            /** @var Mail $model */
            return [
                'class' => $model->isIncoming() ? 'bg-success' : 'bg-warning',
            ];
        },
        'columns' => [
            [
                'label' => false,
                'enableSorting' => false,
                'attribute' => 'id',
                'contentOptions' => ['style' => 'width: 30px;'],
                'content' => function ($model) {
                    /** @var Mail $model */
                    return $model->isIncoming() ? '<i class="mdi mdi-arrow-bottom-left"></i>' : '<i class="mdi mdi-arrow-top-right"></i>';
                },
            ],
            [
                'class' => LinkColumn::className(),
                'attribute' => 'id',
            ],
            [
                'attribute' => 'created_at',
                'contentOptions' => ['style' => 'width: 250px;'],
                'content' => function ($model) {
                    /** @var Mail $model */
                    return date(DATE_LONG_DATE_SEC, strtotime($model->created_at));
                },
            ],
            [
                'attribute' => 'from',
                'contentOptions' => ['class' => 'col-sm-3'],
            ],
            [
                'attribute' => 'to',
                'contentOptions' => ['class' => 'col-sm-3'],
            ],
            'subject',
            [
                'attribute' => 'files',
                'content' => function ($model) {
                    /** @var Mail $model */
                    return implode(', ', array_map([$model, 'getDownloadUrlFile'], explode(',', $model->files)));
                },
            ],
            [
                'attribute' => 'id',
                'label' => false,
                'format' => 'raw',
                'value' => function ($model) {
                    /** @var Mail $model */
                    return $model->isIncoming() ? Html::a('<i class="mdi mdi-arrow-right-bold"></i> SD',
                        Url::toRoute(['send-to-sd', 'id' => $model->id]),
                        ['target' => '_blank', 'class' => 'btn btn-xs btn-default']) : '';
                },
            ]
        ],
    ]); ?>

    <?php Pjax::end(); ?></div>
