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

            $sLog = $logger->dump();
            $logger->clear();
            //

            if( preg_match('|queued\s+as\s+\b([a-f0-9]+)\b|mi', $sLog, $a) ) {
                $model->mail_mta_id = $a[1];
            }

            if( !$model->save() ) {
                Yii::error('Error save mail data: ' . print_r($model->getErrors(), true) . ' attributes = ' . print_r($model->attributes, true));
            }
            Maillog::addLogString($model->mail_id, $sLog, Maillog::MAILLOG_TYPE_SEND);
        }
    }

    /**
     *
     */
    public function actionTest() {
        $sLog = <<<EOT
++ Starting Swift_SmtpTransport
<< 220 mailhost13.educom.ru ESMTP Postfix (Ubuntu)

>> EHLO [127.0.0.1]

<< 250-mailhost13.educom.ru
250-PIPELINING
250-SIZE 40960000
250-VRFY
250-ETRN
250-ENHANCEDSTATUSCODES
250-8BITMIME
250 DSN

++ Swift_SmtpTransport started
>> MAIL FROM:<mail@active.ru>

<< 250 2.1.0 Ok

>> RCPT TO:<devedumos@gmail.com>

<< 250 2.1.5 Ok

>> DATA

<< 354 End data with <CR><LF>.<CR><LF>

>>
.

<< 250 2.0.0 Ok: queued as F011A1811A3
EOT;

        if( preg_match('|queued\s+as\s+\b([a-f0-9]+)\b|mi', $sLog, $a) ) {
            echo "Matched: {$a[1]}\n";
        }
        else {
            echo "Not Matched\n";
        }
    }

    /**
     *
     */
    public function actionGetMtaLog() {
        $list = ['F011A1811A3'];
        $sCommand = 'grep -e ' . implode(' -e ', $list) . ' /var/log/mail.log | grep status';
        $sCommand = 'ssh suser@10.128.1.14 ' . $sCommand;
        $res = array();
        exec($sCommand, $res);
        foreach( $res as $sres ) {
            preg_match('#postfix/smtp\[\d*\]: ([\w\d]*).*status=(\w*) (.*+)#', $sres, $matches);
            $uid    = $matches[1];
            $status = $matches[2];
            $info   = $matches[3];
        }
/*
sudo ssh  -i ../../mosedu.ru/events.mosedu.ru/.ssh/id_rsa suser@10.128.1.14 grep -e 'F011A1811A3' /var/log/mail.log

Apr 28 13:09:59 mx2 postfix/smtpd[29050]: F011A1811A3: client=unknown[192.168.212.244]
Apr 28 13:10:00 mx2 postfix/cleanup[26905]: F011A1811A3: message-id=<0ba69fe78ea1684d9b17365775681977@swift.generated>
Apr 28 13:10:00 mx2 postfix/cleanup[26905]: F011A1811A3: info: header Subject: test subject from unknown[192.168.212.244]; from=<mail@active.ru> to=<devedumos@gmail.com> proto=ESMTP helo=<[127.0.0.1]>
Apr 28 13:10:00 mx2 postfix/qmgr[1483]: F011A1811A3: from=<mail@active.ru>, size=1101, nrcpt=1 (queue active)
Apr 28 13:10:00 mx2 postfix/smtp[29999]: F011A1811A3: to=<devedumos@gmail.com>, relay=gmail-smtp-in.l.google.com[173.194.222.27]:25, delay=0.77, delays=0.16/0/0.27/0.33, dsn=2.0.0, status=sent (250 2.0.0 OK 1461838200 i75si4479842lfg.110 - gsmtp)
Apr 28 13:10:00 mx2 postfix/qmgr[1483]: F011A1811A3: removed
*/
    }
}
