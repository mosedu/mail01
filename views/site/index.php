<?php

/* @var $this yii\web\View */

$this->title = 'Отправитель почты';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Почтовая система</h1>

        <p class="lead">Содержит API для отправки писем и набор сервисных скриптов для отправки их далее.</p>

        <!-- p><a class="btn btn-lg btn-success" href="http://www.yiiframework.com">Get started with Yii</a></p -->
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2>Домены</h2>

                <p>Для отправки писем с определенного домена, необходимо добавить его в спиоск доменов.
                    При отправки писем необходимо использовать ключ API, сгенерированный для этого домена.
                    Адрес &quot;От&quot; будет автоматически добавлен в письма с этого домена.
                </p>

                <!-- p><a class="btn btn-default" href="http://www.yiiframework.com/doc/">Yii Documentation &raquo;</a></p -->
            </div>
            <div class="col-lg-4">
                <h2>Письма</h2>

                <p>Минимальный набор полей для отправки письма:
                    <strong>domainkey</strong>  ключик для апи, определяет домен, для которого работает отправка писем, обязательный параметр<br />
                    <strong>to</strong> email кому письмо, обязательный параметр<br />
                    <strong>subject</strong> тема, обязательный параметр<br />
                    <strong>text</strong> plain текст письма, обязательный параметр, необходим или text, или html, или оба<br />
                </p>

                <!-- p><a class="btn btn-default" href="http://www.yiiframework.com/forum/">Yii Forum &raquo;</a></p -->
            </div>
            <div class="col-lg-4">
                <h2>Логи</h2>

                <p>При получении письма, ему автоматически присвавается id, по которому можно будет посмотреть логи его отправки.</p>

                <!-- p><a class="btn btn-default" href="http://www.yiiframework.com/extensions/">Yii Extensions &raquo;</a></p -->
            </div>
        </div>

    </div>
</div>
