<?php
/**
 * Created by PhpStorm.
 * User: KozminVA
 * Date: 18.04.2016
 * Time: 12:15
 */

namespace app\components;

use yii;

class MailgateAdapter {

    public $url = 'http://mail01.dev/1/mail';

    /**
     * @param array $aData
     *     'apikey' ключик для апи, определяет домен, для которого работает отправка писем, обязательный параметр
     *     'to'     email кому письмо, обязательный параметр
     *     'text'   plain текст письма, обязательный параметр, необходим или text, или html, или оба
     *     'html'   html текст письма
     *     'toname' имя кому, не обязательно
     * @return array
     */

    public function send($aData = []) {
        $this->testInputData($aData);
        return $this->sendByFile($aData);
    }

    /**
     * Проверяем входняе данные
     *
     * @param array $aData
     * @throws \Exception
     */
    public function testInputData($aData = []) {
        $aReq = ['apikey', 'to', ];
        foreach( $aReq As $v ) {
            if( !isset($aData[$v]) ) {
                Yii::info('testInputData: not exists ' . $v);
                throw new \Exception('Not found required parameter "'.$v.'"');
            }
        }

        if( !isset($aData['text']) && !isset($aData['html']) ) {
            Yii::info('testInputData: not exists text or html');
            throw new \Exception('Not found required parameter "text" or "html"');
        }
    }

    /**
     * @param array $aData
     * @param array $aHeaders
     * @return string
     */
    public function sendByFile($aData, $aHeaders = []) {
        $apost = [
            'mail_to' => $aData['to'],
        ];
        foreach(['text', 'html', 'from', 'fromname'] As $v) {
            if( isset($aData[$v]) ) {
                $apost['mail_' . $v] = $aData[$v];
            }
        }

        $aOpt = [
            'method' => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded' . "\r\n"
                       . 'Accept: application/json; q=1.0, */*; q=0.1' . "\r\n",
            'content' => http_build_query($apost),
//            'max_redirects' => '0',
//            'ignore_errors' => '1'
        ];

        \Yii::info('sendByFile: aOpt = ' . print_r($aOpt, true));
        $result = file_get_contents(
            $this->url,
            false,
            stream_context_create(['http' => $aOpt])
        );

        return $result;
    }
}