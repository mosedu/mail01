<?php

namespace app\modules\api1\controllers;

use yii\rest\ActiveController;
use app\components\DomainAuthBehavior;

class MailController extends ActiveController
{
    public $modelClass = 'app\modules\api1\models\Mail';

    public $domain = null;

    public function behaviors() {
        $behaviors = parent::behaviors();

        $behaviors['domainauth'] = [
            'class' => DomainAuthBehavior::className(),
            'modelDomainFields' => [
                'mail_domen_id' => 'domain_id',
            ],
        ];

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $aActions = parent::actions();

        // убиваем ненужные экшены
        foreach(['update', 'delete'] As $act) {
            if( isset($aActions[$act]) ) {
                unset($aActions[$act]);
            }
        }

        return $aActions;
    }


//    public function actionIndex()
//    {
//        return $this->render('index');
//    }
}
