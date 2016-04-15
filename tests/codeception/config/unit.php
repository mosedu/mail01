<?php
/**
 * Application configuration for unit tests
 */
$aConf =  yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../../config/web.php'),
    require(__DIR__ . '/config.php'),
    [

    ]
);

return $aConf;
