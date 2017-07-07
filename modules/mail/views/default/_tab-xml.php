<?php

/* @var $this yii\web\View */
/* @var $data string */

$dom = new DOMDocument;
$dom->preserveWhiteSpace = false;
$dom->loadXML($data);
$dom->formatOutput = true;

$geshi = new GeSHi($dom->saveXml(), 'xml');
$geshi->set_overall_style('font-family:inherit;');

echo $geshi->parse_code();
