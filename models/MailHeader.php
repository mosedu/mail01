<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%mail_header}}".
 *
 * @property string $mhead_id
 * @property string $mhead_mail_id
 * @property string $mhead_headers
 *
 */
class MailHeader extends \yii\db\ActiveRecord
{
    public $_headers = null;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%mail_header}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mhead_mail_id'], 'required'],
            [['mhead_mail_id'], 'integer'],
            [['mhead_headers'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mhead_id' => 'Id',
            'mhead_mail_id' => 'Письмо',
            'mhead_headers' => 'Заголовки',
        ];
    }

    /**
     *
     * Пока список таких заголовков:
     * Cc
     * Priority
     * Sender
     * X-Confirm-Reading-To
     * X-Mailer
     * X-Distribution: bulk
     * X-Priority
     *
     */
    public static function getAvailableHeaders()
    {
        return [
            'Cc',
            'Priority',
            'Sender',
            'X-Confirm-Reading-To',
            'X-Mailer',
            'X-Distribution',
            'X-Priority',
        ];
    }


    /**
     * @param string $sHeaderName
     * @param string $sDefault
     * @return string
     */
    public function getHeaderValue($sHeaderName = '', $sDefault = null) {
        if( $this->_headers === null ) {
            $this->_headers = unserialize($this->mhead_headers);
        }
        return empty($sHeaderName) ? $this->_headers : (isset($this->_headers[$sHeaderName]) ? $this->_headers[$sHeaderName] : $sDefault);
    }

}
