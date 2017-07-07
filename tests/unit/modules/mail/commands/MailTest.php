<?php

namespace app\tests\unit\modules\mail\commands;

use Codeception\Test\Unit;
use Yii;

/**
 * Class MailTest
 *
 * @package app\tests\unit\modules\mail\commands
 */
class MailTest extends Unit
{
    /**
     * @var \PhpMimeMailParser\Parser
     */
    private $parser;

    protected function setUp()
    {
        $mailSrc = <<<SRC
From test-from@local.dev Fri Jul 07 11:25:11 2017
Return-path: <test-from@local.dev>
Envelope-to: test-to@local.dev
Delivery-date: Fri, 07 Jul 2017 11:25:11 +0300
Date: Fri, 07 Jul 2017 13:24:49 UT
Subject: =?utf-8?B?dGVzdCBzdWJqZWN0?=
MIME-Version: 1.0
Content-Type: multipart/mixed; boundary="8bce61888acbdb04c254df0c3f3a3a58"
From: <test-from@local.dev>
To: <test-to@local.dev>

--8bce61888acbdb04c254df0c3f3a3a58
Content-Type: text/html; charset=windows-1251
Content-Transfer-Encoding: base64



--8bce61888acbdb04c254df0c3f3a3a58
Content-Type: application/xml; name="from.xml"
Content-Transfer-Encoding: base64
Content-Disposition: attachment; filename="from.xml"

PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPENJTSBDSU1WRVJTSU9OPSIy
LjAiIERURFZFUlNJT049IjIuMiI+CiAgPERFQ0xBUkFUSU9OPgogICAgPERFQ0xHUk9VUD4KICAg
ICAgPFZBTFVFLk9CSkVDVD4KICAgICAgICA8SU5TVEFOQ0UgQ0xBU1NOQU1FPSJIZWFkZXIiPgog
ICAgICAgICAgPFBST1BFUlRZIE5BTUU9IkRhdGUiIFRZUEU9IlN0cmluZyI+CiAgICAgICAgICAg
IDxWQUxVRT4yMDE3LTA3LTA3IDEzOjI0OjQ4PC9WQUxVRT4KICAgICAgICAgIDwvUFJPUEVSVFk+
CiAgICAgICAgICA8UFJPUEVSVFkgTkFNRT0iQXBwbGljYXRpb24iIFRZUEU9IlN0cmluZyI+CiAg
ICAgICAgICAgIDxWQUxVRT5QT1MtQVRNIFNlcnZpY2UgRGVzazwvVkFMVUU+CiAgICAgICAgICA8
L1BST1BFUlRZPgogICAgICAgIDwvSU5TVEFOQ0U+CiAgICAgIDwvVkFMVUUuT0JKRUNUPgogICAg
ICA8VkFMVUUuT0JKRUNUPgogICAgICAgIDxJTlNUQU5DRSBDTEFTU05BTUU9IkFTU0lHTiI+CiAg
ICAgICAgICA8UFJPUEVSVFkgTkFNRT0iQlRFX05VTSIgVFlQRT0iU3RyaW5nIj4KICAgICAgICAg
ICAgPFZBTFVFPjE3MDcwNzAyNzQ8L1ZBTFVFPgogICAgICAgICAgPC9QUk9QRVJUWT4KICAgICAg
ICAgIDxQUk9QRVJUWSBOQU1FPSJQT1NBVE1fTlVNIiBUWVBFPSJTdHJpbmciPgogICAgICAgICAg
ICA8VkFMVUU+MTA3NDYwPC9WQUxVRT4KICAgICAgICAgIDwvUFJPUEVSVFk+CiAgICAgICAgICA8
UFJPUEVSVFkgTkFNRT0iRU5HSU5FRVJfTkFNRSIgVFlQRT0iU3RyaW5nIj4KICAgICAgICAgICAg
PFZBTFVFPtCQ0YHQutCw0YDQvtCyINCQ0LvQtdC60YHQsNC90LTRgCDQoNCw0LTQuNC60L7QstC4
0Yc8L1ZBTFVFPgogICAgICAgICAgPC9QUk9QRVJUWT4KICAgICAgICAgIDxQUk9QRVJUWSBOQU1F
PSJDUkVBVEVEX1RJTUUiIFRZUEU9IlN0cmluZyI+CiAgICAgICAgICAgIDxWQUxVRT4yMDE3LTA3
LTA3IDEzOjI0OjQ4PC9WQUxVRT4KICAgICAgICAgIDwvUFJPUEVSVFk+CiAgICAgICAgPC9JTlNU
QU5DRT4KICAgICAgPC9WQUxVRS5PQkpFQ1Q+CiAgICA8L0RFQ0xHUk9VUD4KICA8L0RFQ0xBUkFU
SU9OPgo8L0NJTT4K

--8bce61888acbdb04c254df0c3f3a3a58--




SRC;

        $this->parser = Yii::$app->mailParser;
        $this->parser->setText($mailSrc);

        return parent::setUp();
    }

    public function testParseMail()
    {
        $attachments = $this->parser->getAttachments(true);
        $this->assertEquals(1, sizeof($attachments));

        $from = $this->parser->getAddresses('from');
        $this->assertEquals(1, sizeof($from));
        $this->assertArrayHasKey('display', $from[0]);
        $this->assertArrayHasKey('address', $from[0]);
        $this->assertEquals('test-from@local.dev', $from[0]['display']);
        $this->assertEquals('test-from@local.dev', $from[0]['address']);

        $to = $this->parser->getAddresses('to');
        $this->assertEquals(1, sizeof($to));
        $this->assertArrayHasKey('display', $to[0]);
        $this->assertArrayHasKey('address', $to[0]);
        $this->assertEquals('test-to@local.dev', $to[0]['display']);
        $this->assertEquals('test-to@local.dev', $to[0]['address']);

        $this->assertEquals('test subject', $this->parser->getHeader('subject'));
    }

    public function testInsertMail()
    {
        /** @var \app\modules\mail\services\MailService $mailService */
        $mailService = Yii::$container->get('mailService');

        /** @var \app\modules\mail\services\ElasticMailService $elasticService */
        $elasticService = Yii::$container->get('elasticService');

        $mail = $mailService->insertMail($this->parser);
        $this->assertNotNull($mail->id);

//        $result = $elasticService->insert($this->parser, $mail);
    }
}