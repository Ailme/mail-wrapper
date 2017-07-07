<?php
/* @var $this yii\web\View */
/* @var $headers array */

$content = '';

foreach ($headers as $title => $value) {
    if (is_array($value)) {
        $content .= $title . ': ';
        foreach ($value as $item) {
            $content .= ' ' . $item . RN;
        }
    } else {
        $content .= $title . ': ' . $value . RN;
    }
}

$geshi = new GeSHi($content);
$geshi->set_language('email2', true);
$geshi->set_language_path(Yii::getAlias('@app/components/geshi'));
$geshi->set_overall_style('font-family:inherit;');

echo $geshi->parse_code();
