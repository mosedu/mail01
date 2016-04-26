<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use app\models\Mail;

/**
 *
 */
class SendController extends Controller
{
    /**
     * Кол-во неотправленных писем
     */
    public function actionIndex()
    {
        $a = \Yii::$app->db->createCommand('Select SUM(IF(mail_status = '.Mail::MAIL_STATUS_WAITING.', 1, 0)) As waitmsg, SUM(IF(mail_status = '.Mail::MAIL_STATUS_SENDED.', 1, 0)) As sendmsg From ' . Mail::tableName())->queryOne(\PDO::FETCH_ASSOC);
        echo 'Mails: ' . print_r($a, true) . "\n";
    }
}
