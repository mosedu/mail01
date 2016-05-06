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

use app\components\MailgateAdapter;

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
//        $logger = new \Swift_Plugins_Loggers_ArrayLogger();
        /** @var \Swift_Mailer $oSwiftmailer */
//        $oSwiftmailer = Yii::$app->mailer->getSwiftMailer();
//        $oSwiftmailer->registerPlugin(new \Swift_Plugins_LoggerPlugin($logger));

        $aMailers = [];
        $aLoggers = [];
        $aMailersConfig = Yii::$app->params['servers'];

        foreach($a As $model) {
            /** @var Mail $model */
            /** @var Message $oMessage */

            if( !isset($aMailers[$model->mail_domen_id]) ) {
                if( empty($model->domain) || !isset($aMailersConfig[$model->domain->domain_mailer_id]) ) {
                    $sError = 'Not found mailer config for mail ' . print_r($model->attributes, true) . ' domain = ' . print_r(empty($model->domain) ? 'NULL' : $model->domain->attributes, true);
                    Yii::error($sError);
                    echo $sError;
                    continue;
                }
                echo 'Create new mailer ['.$model->mail_domen_id.'] ' . print_r($aMailersConfig[$model->domain->domain_mailer_id]['mailer'], true) . "\n";
                $aMailers[$model->mail_domen_id] = Yii::createObject($aMailersConfig[$model->domain->domain_mailer_id]['mailer']);
                $aLoggers[$model->mail_domen_id] = new \Swift_Plugins_Loggers_ArrayLogger();
                $oSwiftmailer = $aMailers[$model->mail_domen_id]->getSwiftMailer();
                $oSwiftmailer->registerPlugin(new \Swift_Plugins_LoggerPlugin($aLoggers[$model->mail_domen_id]));
            }

//            $oMessage = Yii::$app->mailer->compose();
            $oMessage = $aMailers[$model->mail_domen_id]->compose();

            $oMessage
                ->setFrom(empty($model->mail_fromname) ? $model->mail_from : [$model->mail_from => $model->mail_fromname])
                ->setTo(empty($model->mail_toname) ? $model->mail_to : [$model->mail_to => $model->mail_toname])
                ->setSubject($model->mail_subject);

            $oMsg = $oMessage->getSwiftMessage();
            $headers = $oMsg->getHeaders();

            $headers->addTextHeader('X-Document-Id', $model->mail_id);
            $headers->addTextHeader('Return-Path', $model->mail_from);

            if( !empty($model->headers) ) {
                $aDopHeaders = $model->headers->getHeaderValue();
                foreach($aDopHeaders As $k=>$v) {
                    $headers->addTextHeader($k, $v);
                }
            }

            // Тут добавляем заголовки к письму
            $model->setMailHeaders($oMessage, $aMailersConfig[$model->domain->domain_mailer_id]);

            if( !empty($model->mail_html) ) {
                $oMessage->setHtmlBody($model->mail_html);
            }

            if( !empty($model->mail_text) ) {
                $oMessage->setTextBody($model->mail_text);
            }

            // тут отмечаем данные по попыткам отправки
            $model->mail_send_try += 1;
            $model->mail_send_last_try = new Expression('NOW()');

            try {
                $bSend = $oMessage->send();
                $sErrMsg = '';
            }
            catch(\Exception $e ) {
                $bSend = false;
                $sErrMsg = $e->getMessage() . ' ['.$e->getCode().']';
                echo "Error send mail using {$model->domain->domain_mailer_id} : " . $sErrMsg . "\n";
            }

            if( $bSend ) {
                // если все отправилось, то ставим флажок отправки
                $model->mail_status = Mail::MAIL_STATUS_SENDED;
                echo "Send mail to " . $model->mail_to . " [{$model->mail_id}]\n";
            }
            else if( $model->mail_send_try >= self::MAX_MAIL_SEND_TRY ) {
                // если кол-во попыток достигло максимального - ставим флаг фейла
                $model->mail_status = Mail::MAIL_STATUS_FAILED;
                echo "Fail mail to " . $model->mail_to . " [{$model->mail_id}] tries = {$model->mail_send_try}\n";
            }

            $sLog = $aLoggers[$model->mail_domen_id]->dump() . (!empty($sErrMsg) ? ("\nException message: " . $sErrMsg) : '');
            $aLoggers[$model->mail_domen_id]->clear();
//            $sLog = $logger->dump();
//            $logger->clear();

//            if( preg_match('|queued.+as\s+\b([a-f0-9]+)\b|mi', $sLog, $a) ) {
            if( preg_match('|queued.+\\bas\\s+\\b([^\\b]+)\\b|mi', $sLog, $a) ) {
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

        $sLog1 = <<<EOT
++ Starting Swift_SmtpTransport
<< 220 smtp2m.mail.yandex.net ESMTP (Want to use Yandex.Mail for your domain? Visit http://pdd.yandex.ru)

>> EHLO [127.0.0.1]

<< 250-smtp2m.mail.yandex.net
250-8BITMIME
250-PIPELINING
250-SIZE 42991616
250-AUTH LOGIN PLAIN XOAUTH2
250-DSN
250 ENHANCEDSTATUSCODES

>> AUTH LOGIN

<< 334 VXNlcm5hbWU6

>> YWN0aW9uLnJlZw==

<< 334 UGFzc3dvcmQ6

>> UWF6c0U0UmZ2Z1k3

<< 235 2.7.0 Authentication successful.

++ Swift_SmtpTransport started
>> MAIL FROM:<action.reg@yandex.ru>

<< 250 2.1.0 <action.reg@yandex.ru> ok

>> RCPT TO:<KozminVA@edu.mos.ru>

<< 250 2.1.5 <KozminVA@edu.mos.ru> recipient ok

>> DATA

<< 354 Enter mail, end with "." on a line by itself

>>
.

<< 250 2.0.0 Ok: queued on smtp2m.mail.yandex.net as 1462452243-FOT9J2j3Gv-i3ZaBjQX
EOT;

        if( preg_match('|queued.+\\bas\\s+\\b([^\\b]+)\\b|mi', $sLog, $a) ) {
            echo "sLog Matched: {$a[1]}\n";
        }
        else {
            echo "sLog Not Matched\n";
        }
        if( preg_match('|queued.+\\bas\\s+\\b([^\\b]+)\\b|mi', $sLog1, $a) ) {
            echo "sLog1 Matched: {$a[1]}\n";
        }
        else {
            echo "sLog1 Not Matched\n";
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

    public function actionAdapter() {
        $o = new MailgateAdapter();
        $aRet = $o->send([
            'domainkey' => 'apikey-active',
            'to' => 'KozminVA@edu.mos.ru',
            'toname' => 'Vitechke',
            'fromname' => 'Hard core comp',
            'subject' => 'Письмо из коммандной строки',
            'text' => 'Простой текст' . "\n\n" . 'В несколько строк' . "\n\n" . 'Спасибо за внимание' . "\n",
        ]);

        echo "\n" . print_r($aRet, true) . "\n";
//        $o->setUrl('http://yandex.ru?text=aabbcc&page=3')
//            ->setMethod('get')
//            ->addData(['site' => 'google.ru', 'redirect' => 'no',])
//            ->prepareRequest();
//
//        $o = new MailgateAdapter();
//        $o->setUrl('http://yandex.ru?text=aabbcc&page=3')
//            ->setMethod('post')
//            ->addData(['site' => 'google.ru', 'redirect' => 'no',])
//            ->setTumeout(2)
//            ->setUseragent('Mozilla/5.2-MS-Windows')
//            ->addHeader([
//                'Content-type' => 'application/x-www-form-urlencoded',
//                'Accept' => 'application/json; q=1.0, */*; q=0.1',
//            ])
//            ->prepareRequest();

    }
}
