<?php

use app\modules\mail\Module;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\modules\mail\models\Mail */

$this->title = $model->subject;
$this->params['breadcrumbs'][] = $this->title;

/** @var \PhpMimeMailParser\Parser $Parser */
$Parser = Yii::$app->mailParser;
$Parser->setText($model->raw);

$headers = $Parser->getHeaders();

$xml = [];
$files = [];
foreach ($Parser->getAttachments(true) as $attachment) {
    $files[] = $attachment->getFilename();

    if (preg_match('#\.xml$#ui', $attachment->getFilename())) {
        $xml[] = [
            'title' => $attachment->getFilename(),
            'data' => $attachment->getContent(),
        ];
    };
}

$files = implode(', ', array_map([$model, 'getDownloadUrlFile'], $files));
$from = implode(', ', array_column($Parser->getAddresses('from'), 'address'));
$to = implode(', ', array_column($Parser->getAddresses('to'), 'address'));
$text = (string)$Parser->getMessageBody('text');
$html = (string)$Parser->getMessageBody('html');

?>
<div class="mail-view">
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-sm-12">
            <?= Html::a('<i class="glyphicon glyphicon-envelope"></i> ' . Module::t('module', 'SAVE_MAIL'),
                Url::toRoute(['save', 'id' => $model->id]),
                ['target' => '_blank', 'class' => 'btn btn-primary']) ?>

            <?php if ($model->isIncoming()): ?>
                <?= Html::a('<i class="mdi mdi-arrow-right-bold"></i> SD',
                    Url::toRoute(['send-to-sd', 'id' => $model->id]),
                    ['target' => '_blank', 'class' => 'btn btn-info']) ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-condensed">
            <tr>
                <th><?= $model->attributeLabels()['id'] ?></th>
                <td>
                    <?= $model->id ?>
                </td>
                <th><?= $model->attributeLabels()['created_at'] ?></th>
                <td><?= date(DATE_LONG_DATE_SEC, strtotime($model->created_at)) ?></td>
            </tr>
            <tr>
                <th><?= $model->attributeLabels()['type'] ?></th>
                <td><?= $model->getTypesName() ?></td>
                <th><?= $model->attributeLabels()['subject'] ?></th>
                <td><?= (string)$Parser->getHeader('subject') ?></td>
            </tr>
            <tr>
                <th><?= $model->attributeLabels()['from'] ?></th>
                <td><?= $from ?></td>
                <th><?= $model->attributeLabels()['to'] ?></th>
                <td><?= $to ?></td>
            </tr>
            <tr>
                <th><?= $model->attributeLabels()['files'] ?></th>
                <td colspan="3">
                    <?= $files ?>
                </td>
            </tr>
        </table>
    </div>

    <?php
    $items = [];
    $items[] = [
        'label' => Module::t('module', 'HEADERS'),
        'content' => $this->render('_tab-headers', compact('headers')),
        'active' => true,
    ];

    if (!empty($text)) {
        $items[] = [
            'label' => $model->attributeLabels()['text'],
            'content' => Yii::$app->formatter->asNtext($text),
        ];
    }

    if (!empty($html)) {
        $items[] = [
            'label' => $model->attributeLabels()['html'],
            'content' => $html,
        ];
    }

    if (sizeof($xml)) {
        foreach ($xml as $item) {
            $items[] = [
                'label' => $item['title'],
                'content' => $this->render('_tab-xml', $item),
            ];
        }
    }
    ?>
    <?= Tabs::widget([
        'items' => $items,
    ]);
    ?>
</div>
