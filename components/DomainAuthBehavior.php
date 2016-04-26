<?php
/**
 * Created by PhpStorm.
 * User: KozminVA
 * Date: 19.04.2016
 * Time: 12:54
 */

namespace app\components;

use yii;
use yii\base\Behavior;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\Request;

use app\models\Domain;

/**
 * Class DomainAuthBehavior
 * @package app\components
 *
 *
 * мы будем по ключу искать домен, для которого планируется действие в API
 * если не найден домен, то бросаем исключение, как у неавторизованного пользователя
 */

class DomainAuthBehavior extends Behavior {
    // имя параметра для ключика для домена
    const DOMAIN_KEY_PARAM_NAME = 'domainkey';

    // сюда можно положить дополнительные имена для ключика
    public $apikeyname = [];

    /**
     * @var array
     * список полей модели для которых нужно назначить поля домена
     * ключ - имя поля в модели Mail
     * значение - или поле в модели Domain, или функция, которой передается найденый Domain и возвращается значение для поля Mail
     */
    public $modelDomainFields = [];

    public function events() {
        return [
            Controller::EVENT_BEFORE_ACTION => 'setControllerDomain',
        ];
    }


    /**\
     * @param $event
     * @throws ForbiddenHttpException
     *
     * Ищем ключик в Get или Post параметрах запроса, по ключику ищем домен,
     * если не находим, то бросаем исключение, если находим, присваиваем его модели в контроллере
     *
     */
    public function setControllerDomain($event) {
        /**  */
        // if(  )
        Yii::info('setControllerDomain(): action->id = ' . $this->owner->action->id);
        if( $this->owner->action->id != 'create' ) {
            return;
        }

        $apikeyname = array_merge([self::DOMAIN_KEY_PARAM_NAME], $this->apikeyname);

        Yii::info('setControllerDomain(): apikeyname = ' . print_r($apikeyname, true));

        $request = Yii::$app->request;

        foreach($apikeyname As $sKeyName) {
            $val = $request->getQueryParam($sKeyName);
            if( $val === null ) {
                $val = $request->post($sKeyName);
            }

            Yii::info('setControllerDomain(): val = ' . $val);

            if( $val !== null ) {
                $ob = Domain::find()
                    ->where([
                        'domain_authkey' => $val,
                        'domain_status' => Domain::DOMAIN_STATUS_ACTIVE,
                    ])
                    ->one();

                if( $ob === null ) {
                    throw new ForbiddenHttpException('Domain not found for api key ' . $val);
                }

                Yii::info('setControllerDomain(): domain_id = ' . $ob->domain_id);

                if( count($this->modelDomainFields) > 0 ) {
                    // заполняем поля для домена
                    $this->setModelDataFromDomain($request, $ob);
                }
                $this->owner->domain = $ob;
            }
        }

    }

    /**
     * @param Request $request
     * @param Domain $domen
     *
     * Вносим дополнительные данные в массив параметров из объекта домена
     *
     */
    public function setModelDataFromDomain($request, $domain) {
        $params = $request->getBodyParams();

        if( count($params) > 0 ) {
            foreach($this->modelDomainFields As $k=>$v) {
                if( is_string($v) ) {
                    $params[$k] = $domain->$v;
                }
                else if( $v instanceof \Closure ) {
                    $params[$k] = $v($domain, $params);
                }
            }
            $request->setBodyParams($params);
        }
    }

}