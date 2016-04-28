<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%maillog}}".
 *
 * @property integer $mlog_id
 * @property string $mlog_createtime
 * @property string $mlog_mail_id
 * @property integer $mlog_type
 * @property string $mlog_text
 */
class Maillog extends \yii\db\ActiveRecord
{
    const MAILLOG_TYPE_SEND = 1; // лог отправки письма
    const MAILLOG_TYPE_SMTP = 2; // лог от MTA

    /**
     * @return array
     */
    public function behaviors() {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['mlog_createtime'],
                ],
                'value' => new Expression('NOW()'),
            ],

        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%maillog}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//            [['mlog_createtime'], 'safe'],
            [['mlog_mail_id', 'mlog_type'], 'integer'],
            [['mlog_text'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mlog_id' => 'Id',
            'mlog_createtime' => 'Создан',
            'mlog_mail_id' => 'Письмо',
            'mlog_type' => 'Тип',
            'mlog_text' => 'Текст',
        ];
    }

    /**
     * @param int $nMailId
     * @param string $sLogStr
     * @param int $nType
     */
    public static function addLogString($nMailId = 0, $sLogStr = '', $nType = self::MAILLOG_TYPE_SEND) {
        $oLog = new Maillog();
        $oLog->mlog_type = $nType;
        $oLog->mlog_mail_id = $nMailId;
        $oLog->mlog_text = $sLogStr;
        if( !$oLog->save() ) {
            Yii::error('Error save maillog data: ' . print_r($oLog->getErrors(), true) . ' attributes = ' . print_r($oLog->attributes, true));
        }
    }
}
