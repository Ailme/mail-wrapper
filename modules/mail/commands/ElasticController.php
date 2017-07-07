<?php

namespace app\modules\mail\commands;

use app\modules\mail\models\Mail;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Class ElasticController
 *
 * @package app\modules\mail\commands
 */
class ElasticController extends Controller
{
    public function actionIndex()
    {
        echo 'yii mail/elastic/update' . PHP_EOL;
    }

    /**
     * move mails from DB to Elastic
     */
    public function actionUpdate()
    {
        /** @var \PhpMimeMailParser\Parser $Parser */
        $Parser = Yii::$app->mailParser;

        /** @var \app\modules\mail\services\ElasticMailService $elasticService */
        $elasticService = Yii::$container->get('elasticServices');

        $list = array_column(Mail::find()->select('id')->asArray()->all(), 'id');

        foreach ($list as $id) {
            try {
                $mail = Mail::findOne($id);

                $Parser->setText($mail->raw);

                $result = $elasticService->insert($Parser, $mail);

                $memory = memory_get_peak_usage(true) / 1024 / 1024;

                $this->stdout($result['_id'] . TAB . $memory . RN, Console::FG_GREEN, Console::BOLD);
            } catch (\Exception $e) {
                \Yii::error([
                    'msg' => $e->getMessage(),
                ], 'console');
            }
        };
    }

    public function actionCreateIndex()
    {
        /** @var \app\modules\mail\services\ElasticMailService $elasticService */
        $elasticService = Yii::$container->get('elasticService');
        $elasticService->createIndex();
    }

    public function actionDeleteIndex()
    {
        /** @var \app\modules\mail\services\ElasticMailService $elasticService */
        $elasticService = Yii::$container->get('elasticService');
        $elasticService->deleteIndex();
    }

    /**
     * удаление старых писем
     */
    public function actionDeleteOld()
    {
        $days = 14;

        /** @var \app\modules\mail\services\ElasticMailService $elasticService */
        $elasticService = Yii::$container->get('elasticService');
        $elasticService->deleteOld($days);
    }
}
