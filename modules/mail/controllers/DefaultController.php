<?php

namespace app\modules\mail\controllers;

use app\modules\mail\Module;
use Yii;
use app\modules\mail\models\Mail;
use app\modules\mail\forms\MailSearchForm;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * DefaultController implements the CRUD actions for Mail model.
 */
class DefaultController extends Controller
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Mail models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MailSearchForm();

        $params = Yii::$app->request->queryParams;

        if (Yii::$app->request->getQueryParam('reset')) {
            $this->redirect(['index']);
        }

        $dataProvider = $searchModel->searchElastic($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Mail model.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * donwload file
     *
     * @param $id
     * @param $name
     */
    public function actionDownloadFile($id, $name)
    {
        $model = $this->findModel($id);

        /** @var \app\modules\mail\services\MailService $mailService */
        $mailService = Yii::$container->get('mailService');
        $mailService->downloadFile($name, $model);
    }

    /**
     * donwload mail as file
     *
     * @param $id
     *
     * @return string
     */
    public function actionSave($id)
    {
        $model = $this->findModel($id);

        /** @var \app\modules\mail\services\MailService $mailService */
        $mailService = Yii::$container->get('mailService');

        return $mailService->download($model);
    }

    /**
     * @param $id
     *
     * @return string
     */
    public function actionSendToSd($id)
    {
        $model = $this->findModel($id);

        /** @var \app\modules\mail\services\MailService $mailService */
        $mailService = Yii::$container->get('mailService');
        $mailService->addToSdWithoutParser($model);

        return 'ok';
    }

    /**
     * Finds the Mail model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Mail the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Mail::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Module::t('app', 'REQUESTED_PAGE_NOT_EXIST'));
        }
    }
}
