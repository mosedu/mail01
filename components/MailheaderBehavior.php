<?php
/**
 * Created by PhpStorm.
 * User: KozminVA
 * Date: 26.04.2016
 * Time: 15:18
 */

namespace app\components;

use Yii;
use Closure;
use yii\base\Behavior;
use yii\base\Event;
use yii\db\ActiveRecord;

use app\models\Mail;
use app\models\MailHeader;

class MailheaderBehavior extends Behavior {

    public function events() {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'setMailHeader',
        ];
    }


    /**
     * @param Event $event
     */
    public function setMailHeader($event) {
        /** @var Mail $model */
        $model = $event->sender;

        if( !empty($model->mailHeaders) ) {
            $oHeaders = new MailHeader();
            $oHeaders->attributes = [
                'mhead_mail_id' => $model->mail_id,
                'mhead_headers' => serialize($model->mailHeaders),
            ];
            if( !$oHeaders->save() ) {
                Yii::error('Error save headers for mail ['.$model->mail_id.']: ' . print_r($oHeaders->getErrors(), true));
            }
        }
    }

}