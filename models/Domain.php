<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\AttributeBehavior;
use yii\base\Event;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%domain}}".
 *
 * @property integer $domain_id
 * @property string $domain_createtime
 * @property string $domain_name
 * @property integer $domain_status
 * @property integer $domain_authkey
 * @property integer $domain_authkey_updated
 * @property string $domain_mail_from
 * @property string $domain_mail_fromname
 * @property string $domain_mailer_id
 */
class Domain extends ActiveRecord
{
    const DOMAIN_STATUS_ACTIVE = 1;
    const DOMAIN_STATUS_BLOCKED = 2;

    /**
     * @return array
     */
    public function behaviors() {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['domain_createtime'],
                ],
                'value' => new Expression('NOW()'),
            ],

            // если при создании не указан статус домена, то по умолчанию будет заблокирован
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['domain_status'],
                ],
                'value' => function( $event ) {
                    /** @var Event $event */
                    /** @var Domain $model */
                    $model = $event->sender;
                    return empty($model->domain_status) ? $model::DOMAIN_STATUS_BLOCKED : $model->domain_status;
                },
            ],

            // если нет ключика, то его нужно добавить, и изменить время установки ключика
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['domain_authkey'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['domain_authkey'],
                ],
                'value' => function( $event ) {
                    /** @var Event $event */
                    /** @var Domain $model */
                    $model = $event->sender;
                    if( empty($model->domain_authkey) ) {
                        $model->generateAuthKey();
                    }
                    return $model->domain_authkey;
                },
            ],

        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%domain}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        mb_internal_encoding('UTF-8');
        return [
            [['domain_name', 'domain_mailer_id', ], 'required', ], // 'domain_mail_from',
            [['domain_name', 'domain_mail_from', ], 'filter', 'filter' => 'mb_strtolower', ],
            [['domain_name', ], 'unique', ],
            [['domain_mail_from', ], 'email', ],
            [['domain_name', 'domain_authkey', 'domain_mail_from', 'domain_mail_fromname', 'domain_mailer_id', ], 'string', 'max' => 255],
            [['domain_name', ], 'match', 'pattern' => '|^[a-zа-яё_][-a-z0-9а-яё_\\.]+\\.[a-zа-яё]+$|u', ],

            [['domain_status', ], 'integer', ],
            [['domain_status', ], 'in', 'range' => array_keys(self::getAllStatuses()), ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'domain_id' => 'Id',
            'domain_createtime' => 'Создан',
            'domain_name' => 'Имя',
            'domain_status' => 'Статус',
            'domain_authkey' => 'Ключ',
            'domain_authkey_updated' => 'Изменение ключа',
            'domain_mail_from' => 'Email От',
            'domain_mail_fromname' => 'Имя От',
            'domain_mailer_id' => 'Mailer ID',
        ];
    }

    /**
     * @return array
     */
    public static function getAllStatuses() {
        return [
            self::DOMAIN_STATUS_ACTIVE => 'Активный',
            self::DOMAIN_STATUS_BLOCKED => 'Заблокирован',
        ];
    }

    /**
     * @return array
     */
    public function getStatus() {
        $a = self::getAllStatuses();
        return isset($a[$this->domain_status]) ? $a[$this->domain_status] : '';
    }

    /**
     * @return array
     */
    public static function getAllMailers() {
        $a = Yii::$app->params['servers'];
        $b = array_map(function($el){ return $el['mailer']['transport']['host'] . ' ' . $el['from'] . ' ('.$el['mailer']['transport']['class'].')'; }, $a);
        return $b;
    }

    /**
     * @return array
     */
    public function getMailer() {
        $a = Yii::$app->params['servers'];
        return isset($a[$this->domain_mailer_id]) ? $a[$this->domain_mailer_id] : null;
    }

    /**
     *
     */
    public function generateAuthKey() {
        $this->domain_authkey = Yii::$app->security->generateRandomString(32) . time();
        $this->domain_authkey_updated = new Expression('NOW()');
    }

    /**
     * Список доменов, отфильтрованных опционально по статусу
     *
     * @param int|null/array $nStatus
     * @return array
     */
    public static function getDomainList($nStatus = self::DOMAIN_STATUS_ACTIVE) {
        $aWhere = $nStatus === null ? [] : ['domain_status' => $nStatus];
        return ArrayHelper::map(
            self::find()->where($aWhere)->all(),
            'domain_id',
            'domain_name'
        );
    }
}
