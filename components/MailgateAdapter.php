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

    /** @var array данные для передачи */
    public $_data = [];

    /** @var string метод передачи */
    public $_method = 'POST';

    /** @var array заголовки запроса */
    public $_headers = [];

    /** @var string user agent */
    public $_user_agent = '';

    /** @var float timeout в секундах */
    public $_timeout = 5;

    /** @var array заголовки ответа */
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

//        $aOpt = [
//            'method' => 'POST',
//            'header'  => 'Content-type: application/x-www-form-urlencoded' . "\r\n"
//                       . 'Accept: application/json; q=1.0, */*; q=0.1' . "\r\n",
//            'content' => http_build_query($apost),
////            'max_redirects' => '0',
//            'ignore_errors' => '1',
//        ];

        $context = $this->setMethod('post')
            ->addData($apost)
            ->addHeader(array_merge([
                'Content-type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json; q=1.0, */*; q=0.1',
            ], $aHeaders))
            ->prepareRequest();


        $result = file_get_contents(
            $this->url,
            false,
            $context // stream_context_create(['http' => $aOpt])
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

    /**
     * Установка адреса запроса
     *
     * @param string $sUrl
     * @return $this
     */
    public function setUrl($sUrl = '') {
        if( !empty($sUrl) ) {
            $this->url = $sUrl;
        }
        return $this;
    }

    /**
     * Установка метода запроса
     *
     * @param string $sUrl
     * @return $this
     */
    public function setMethod($sMethod = '') {
        if( !empty($sMethod) ) {
            $this->_method = $sMethod;
        }
        return $this;
    }

    /**
     * Установка user agent
     *
     * @param string $sAgent
     * @return $this
     */
    public function setUseragent($sAgent = '') {
        if( !empty($sAgent) ) {
            $this->_user_agent = $sAgent;
        }
        return $this;
    }

    /**
     * Установка timeout
     *
     * @param float $nTimeout
     * @return $this
     */
    public function setTumeout($nTimeout = 0) {
        if( $nTimeout > 0 ) {
            $this->_timeout = $nTimeout;
        }
        return $this;
    }

    /**
     * Добавление заголовков к запросу
     *
     * @param string|array $sName
     * @return $this
     */
    public function addHeader($sName, $sValue = '') {
        if( !empty($sName) ) {
            if( is_string($sName) ) {
                $sName = [$sName => $sValue];
            }
            foreach($sName As $k=>$v) {
                $this->_headers[$k] = $v;
            }
        }
        return $this;
    }

    /**
     * Добавление заголовков к запросу
     *
     * @param string|array $sName
     * @return $this
     */
    public function addData($sName, $sValue = '') {
        if( !empty($sName) ) {
            if( is_string($sName) ) {
                $sName = [$sName => $sValue];
            }
            foreach($sName As $k=>$v) {
                $this->_data[$k] = $v;
            }
        }
        return $this;
    }

    /**
     * Создание запроса из установленных для него данных
     */
    public function prepareRequest() {
        if( strtolower($this->_method) == 'get' ) {
            // 'http://username:password@hostname:9090/path?arg=value#anchor'
            $a = parse_url($this->url);

            if( !empty($a['query']) ) {
                parse_str($a['query'], $aData);
                $this->addData($aData);
            }

            $a['query'] = http_build_query($this->_data);

            $this->url = $a['scheme'] . '://'
                . (!empty($a['user']) ? $a['user'] : '')
                . (!empty($a['pass']) ? (':' . $a['pass']) : '')
                . (!empty($a['user']) || !empty($a['pass']) ? '@' : '')
                . $a['host']
                . (!empty($a['port']) ? (':' . $a['port']) : '')
                . (!empty($a['path']) ? $a['path'] : '')
                . (!empty($a['query']) ? ('?' . $a['query']) : '');
        }

        $sHeaders = '';
        foreach($this->_headers As $k=>$v) {
            $sHeaders .= $k . ': ' . $v . "\r\n";
        }

        $aOpt = [
            'method' => strtoupper($this->_method),
            'ignore_errors' => '1',
        ];

        if( !empty($sHeaders) ) {
            $aOpt['header'] = $sHeaders;
        }

        if( strtolower($this->_method) == 'post' ) {
            $aOpt['content'] = http_build_query($this->_data);
        }

        if( !empty($this->_user_agent) ) {
            $aOpt['user_agent'] = $this->_user_agent;
        }

        if( $this->_timeout > 0 ) {
            $aOpt['timeout'] = $this->_timeout;
        }

//        echo "url = {$this->url}\naOpt = " . print_r($aOpt, true) . "\n";

        return stream_context_create(['http' => $aOpt]);
    }
}