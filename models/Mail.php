<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

use app\components\OnerequareValidator;
use app\models\MailHeader;
use app\components\MailheaderBehavior;


/**
 * This is the model class for table "{{%mail}}".
 *
 * @property string $mail_id
 * @property integer $mail_domen_id
 * @property string $mail_createtime
 * @property string $mail_from
 * @property string $mail_fromname
 * @property string $mail_to
 * @property string $mail_toname
 * @property string $mail_text
 * @property string $mail_html
 * @property integer $mail_status
 * @property integer $mail_send_try
 * @property integer $mail_send_last_try
 * @property string $mail_subject
 */
class Mail extends ActiveRecord
{
    public $mailHeaders = [];
    /**
     * @return array
     */
    public function behaviors() {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['mail_createtime'],
                ],
                'value' => new Expression('NOW()'),
            ],

            [
                'class' => MailheaderBehavior::className(),
            ],

        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%mail}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mail_to', 'mail_subject', 'mail_domen_id', ], 'required', ],
            [['mail_to', ], 'email', ],
            [['mail_domen_id', 'mail_status', 'mail_send_try', ], 'integer'],
            [['mail_text', 'mail_html'], 'string'],
            [['mail_text', ], OnerequareValidator::className(), 'anotherAttributes' => ['mail_text', 'mail_html', ], ],
            [['mail_from', 'mail_fromname', 'mail_to', 'mail_toname'], 'string', 'max' => 255],
            [['mailHeaders', ], 'testHeaders', ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mail_id' => 'Id',
            'mail_domen_id' => 'Домен',
            'mail_createtime' => 'Создано',
            'mail_from' => 'От',
            'mail_fromname' => 'От',
            'mail_to' => 'Кому',
            'mail_toname' => 'Кому',
            'mail_subject' => 'Тема',
            'mail_text' => 'Текст',
            'mail_html' => 'html',
            'mail_status' => 'Статус',
            'mail_send_try' => 'Попытки',
            'mail_send_last_try' => 'Дата последней попытки',
            'mailHeaders' => 'Дополнительные заголовки',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHeaders() {
        return $this->hasOne(
            MailHeader::className(),
            ['mhead_mail_id' => 'mail_id']
        );
    }

    /**
     * @param string $attribute
     * @param array $params
     */
    public function testHeaders($attribute, $params) {
        $aHeaderList = [];
        $aHeaderNames = MailHeader::getAvailableHeaders();
        $aHeaderNames = array_combine(array_map('strtolower', $aHeaderNames), $aHeaderNames);
        $bSetHeader = true;

        foreach($this->$attribute As $k=>$v) {
            $sName = strtolower($k);
            if( !isset($aHeaderNames[$sName]) ) {
                $this->addError($attribute, $attribute . ' has not available header ' . $k);
                $bSetHeader = false;
            }
            else {
                $aHeaderList[$aHeaderNames[$sName]] = $v;
            }
        }

        if( $bSetHeader ) {
            $this->$attribute = $aHeaderList;
        }
    }
}
