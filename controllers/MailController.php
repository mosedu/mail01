<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\widgets\ActiveForm;
use yii\web\Response;
use yii\helpers\Html;

use app\models\Mail;
use app\models\MailSearch;
use app\models\Domain;

/**
 * MailController implements the CRUD actions for Mail model.
 */
class MailController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
//                        'actions' => ['index'],
                        'roles' => ['@'],
                    ],
                ],
            ],

            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Mail models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Mail model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Mail model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Mail();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->mail_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Mail model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->mail_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Mail model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
//        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function actionLog($id)
    {
        $model = $this->findModel($id);

        if( Yii::$app->request->isPost ) {
            return $this->renderAjax('log', ['model' => $model,]);
        }
        else {
            return $this->render('log', ['model' => $model,]);
        }
    }

    /**
     * @return mixed
     */
    public function actionManualform()
    {
        $model = new Mail();

        if( Yii::$app->request->isAjax ) {
            if( $model->load(Yii::$app->request->post()) ) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                $aValidate = ActiveForm::validate($model);
                if( count($aValidate) == 0 ) {
                    /** @var Domain $domain */
                    $domain = Domain::findOne(['domain_id' => $model->mail_domen_id]);
                    $aDomainData = ($domain !== null) && isset(Yii::$app->params['servers'][$domain->domain_mailer_id]) ? Yii::$app->params['servers'][$domain->domain_mailer_id] : null ;
                    $model->mail_from = ($aDomainData !== null) ? $aDomainData['from'] : '';
                    if( empty($model->mail_fromname) ) {
                        $model->mail_fromname = $model->mail_from;
                    }
                    if( !$model->save() ) {
                        $sId = Html::getInputId($model, '');
                        $aValidate[$sId] = ['Error on save mail: ' . print_r($model->getErrors(), true)];
                        Yii::error('Error on save mail: ' . print_r($model->getErrors(), true) . ' attributes = ' . print_r($model->attributes, true));
                    }
                }
                return $aValidate;
            }
            return $this->renderAjax('manualform', ['model' => $model,]);
        }

        if( $model->load(Yii::$app->request->post()) ) {
            return  $this->refresh(); // $this->redirect(['index', ]);
        } else {
            return $this->render('manualform', ['model' => $model,]);
        }

    }

    /**
     * Finds the Mail model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Mail the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Mail::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
