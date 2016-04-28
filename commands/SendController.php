<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii;
use yii\console\Controller;
use yii\swiftmailer\Message;
use yii\db\Expression;

use app\models\Mail;
use app\models\Maillog;

/**
 *
 */
class SendController extends Controller
{
    const MAX_MAIL_SEND_TRY = 3;

    /**
     * Кол-во неотправленных писем
     */
    public function actionIndex()
    {
        $a = \Yii::$app->db->createCommand('Select SUM(IF(mail_status = '.Mail::MAIL_STATUS_WAITING.', 1, 0)) As waitmsg, SUM(IF(mail_status = '.Mail::MAIL_STATUS_SENDED.', 1, 0)) As sendmsg From ' . Mail::tableName())->queryOne(\PDO::FETCH_ASSOC);
        echo 'Mails: ' . print_r($a, true) . "\n";
    }

    /**
     * Отправка писем
     */
    public function actionMail()
    {
        $a = Mail::find()
            ->where([
                'and',
                ['mail_status' => Mail::MAIL_STATUS_WAITING],
                ['<', 'mail_send_try', self::MAX_MAIL_SEND_TRY],
            ])
            ->with(['headers', 'domain',])
            ->limit(100)
            ->all();

        // To use the ArrayLogger
        $logger = new \Swift_Plugins_Loggers_ArrayLogger();
        /** @var \Swift_Mailer $oSwiftmailer */
        $oSwiftmailer = Yii::$app->mailer->getSwiftMailer();
        $oSwiftmailer->registerPlugin(new \Swift_Plugins_LoggerPlugin($logger));

        foreach($a As $model) {
            /** @var Mail $model */
            /** @var Message $oMessage */

            $oMessage = Yii::$app->mailer->compose();

            $oMessage
                ->setFrom(empty($model->mail_fromname) ? $model->mail_from : [$model->mail_from => $model->mail_fromname])
                ->setTo(empty($model->mail_toname) ? $model->mail_to : [$model->mail_to => $model->mail_toname])
                ->setSubject($model->mail_subject);

            $oMsg = $oMessage->getSwiftMessage();
            $headers = $oMsg->getHeaders();

            $headers->addTextHeader('X-Document-Id', $model->mail_id);

            if( !empty($model->headers) ) {
                $aDopHeaders = $model->headers->getHeaderValue();
                foreach($aDopHeaders As $k=>$v) {
                    $headers->addTextHeader($k, $v);
                }
            }

            $model->setMailHeaders($oMessage);

            if( !empty($model->mail_html) ) {
                $oMessage->setHtmlBody($model->mail_html);
            }

            if( !empty($model->mail_text) ) {
                $oMessage->setTextBody($model->mail_text);
            }

            // тут отмечаем данные по попыткам отправки
            $model->mail_send_try += 1;
            $model->mail_send_last_try = new Expression('NOW()');

            if( $oMessage->send() ) {
                // если все отправилось, то ставим флажек отправки
                $model->mail_status = Mail::MAIL_STATUS_SENDED;
                echo "Send mail to " . $model->mail_to . " [{$model->mail_id}]\n";
            }
            else if( $model->mail_send_try >= self::MAX_MAIL_SEND_TRY ) {
                // если кол-во попыток достигло максимального - ставим флаг фейла
                $model->mail_status = Mail::MAIL_STATUS_FAILED;
                echo "Fail mail to " . $model->mail_to . " [{$model->mail_id}]\n";
            }

//            $model->setMailHeaders($oMessage);

            if( !$model->save() ) {
                Yii::error('Error save mail data: ' . print_r($model->getErrors(), true) . ' attributes = ' . print_r($model->attributes, true));
            }
            $sLog = $logger->dump();
            $logger->clear();
            Maillog::addLogString($model->mail_id, $sLog, Maillog::MAILLOG_TYPE_SEND);
        }
    }

}
