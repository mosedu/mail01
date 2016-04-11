<?php
/**
 * Created by PhpStorm.
 * User: KozminVA
 * Date: 19.11.2015
 * Time: 13:04
 */

namespace app\components;

use yii;

class Migration extends yii\db\Migration {


    public function refreshCache()
    {
        Yii::$app->db->schema->refresh();
        Yii::$app->db->schema->getTableSchemas();
    }

}