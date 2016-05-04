<?php

namespace app\controllers;

use Yii;
use app\models\Domain;
use app\models\DomainSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * DomainController implements the CRUD actions for Domain model.
 */
class DomainController extends Controller
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
     * Lists all Domain models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DomainSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Domain model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->redirect(['index', ]);

//        return $this->render('view', [
//            'model' => $this->findModel($id),
//        ]);
    }

    /**
     * Creates a new Domain model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        return $this->actionUpdate();

//        $model = new Domain();
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->domain_id]);
//        } else {
//            return $this->render('create', [
//                'model' => $model,
//            ]);
//        }
    }

    /**
     * Updates an existing Domain model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id = 0)
    {
        if( $id == 0 ) {
            $model = new Domain();
        }
        else {
            $model = $this->findModel($id);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', ]);
//            return $this->redirect(['view', 'id' => $model->domain_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Domain model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
//        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $id
     * @return mixed
     */
    public function actionBlock($id)
    {
        return $this->setDomainStatus($id, Domain::DOMAIN_STATUS_BLOCKED);
    }

    /**
     * @param integer $id
     * @return mixed
     */
    public function actionUnblock($id)
    {
        return $this->setDomainStatus($id, Domain::DOMAIN_STATUS_ACTIVE);
    }

    /**
     * @param integer $id
     * @param integer $nStatus
     * @return mixed
     */
    public function setDomainStatus($id, $nStatus)
    {
        $model = $this->findModel($id);
        $model->domain_status = $nStatus;
        if( !$model->save() ) {
            Yii::error('Error set domain status ' . print_r($model->getErrors(), true) . ' attributes = ' . print_r($model->attributes, true));
        }
        $this->redirect(['index']);
    }

    /**
     * Finds the Domain model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Domain the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Domain::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
