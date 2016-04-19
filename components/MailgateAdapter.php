<?php
/**
 * Created by PhpStorm.
 * User: KozminVA
 * Date: 18.04.2016
 * Time: 12:15
 */

namespace app\components;

class MailgateAdapter {

    /** @var string адрес по которому пихаем данные */
    public $url = 'http://mail01.dev/1/mail';

    /** @var array  */
    public $responseHeaders = [];

    /**
     * @param array $aData
     *     'domainkey'  ключик для апи, определяет домен, для которого работает отправка писем, обязательный параметр
     *     'to'      email кому письмо, обязательный параметр
     *     'subject' тема, обязательный параметр
     *     'text'    plain текст письма, обязательный параметр, необходим или text, или html, или оба
     *     'html'    html текст письма
     *     'toname'  имя кому, не обязательно
     * @return array
     */

    public function send($aData = []) {
        $this->testInputData($aData);
        return $this->sendByFile($aData);
    }

    /**
     * Проверяем входные данные
     *
     * @param array $aData
     * @throws \Exception
     */
    public function testInputData($aData = []) {
        $aReq = ['domainkey', 'to', 'subject', ];
        foreach( $aReq As $v ) {
            if( !isset($aData[$v]) ) {
                throw new \Exception('Not found required parameter "'.$v.'"');
            }
        }

        if( !isset($aData['text']) && !isset($aData['html']) ) {
            throw new \Exception('Not found required parameter "text" or "html"');
        }
    }

    /**
     * @param array $aData
     * @param array $aHeaders
     * @return string
     */
    public function sendByFile($aData, $aHeaders = []) {
        $apost = [];

        foreach(['to', 'text', 'html', 'from', 'fromname', 'subject'] As $v) {
            if( isset($aData[$v]) ) {
                $apost['mail_' . $v] = $aData[$v];
                unset($aData[$v]);
            }
        }

        $apost = array_merge($apost, $aData);

        $aOpt = [
            'method' => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded' . "\r\n"
                       . 'Accept: application/json; q=1.0, */*; q=0.1' . "\r\n",
            'content' => http_build_query($apost),
//            'max_redirects' => '0',
            'ignore_errors' => '1',
        ];

        $result = file_get_contents(
            $this->url,
            false,
            stream_context_create(['http' => $aOpt])
        );

        $this->responseHeaders = $http_response_header;

        return $result;
    }

    /**
     * Получаем заголовки ответа
     * @return array
     */
    public function getResponseHeaders() {
        return $this->responseHeaders;
    }
}