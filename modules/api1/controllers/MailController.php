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

        // мы можем еще передать в параметрах заголовки для письма и вставить их в поле mailHeaders модели письма
        $behaviors['domainauth'] = [
            'class' => DomainAuthBehavior::className(),
            'modelDomainFields' => [
                'mail_domen_id' => 'domain_id',
//                'mail_from' => function($domain, $param) { return isset($param['mail_from']) ? $param['mail_from'] : $domain->domain_mail_from; },
//                'mail_fromname' => function($domain, $param) { return isset($param['mail_fromname']) ? $param['mail_fromname'] : $domain->domain_mail_fromname; },
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
