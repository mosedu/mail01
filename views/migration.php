<?php
/**
 * This view is used by console/controllers/MigrateController.php
 * The following variables are available in this view:
 */
/* @var $className string the new migration class name */

echo "<?php\n";
?>

use yii\db\Schema;
use app\components\Migration;

class <?= $className ?> extends Migration
{
    public function up()
    {
        // need refrash cache after change table strusture
        // $this->refreshCache();
    }

    public function down()
    {
        echo "<?= $className ?> cannot be reverted.\n";

        // need refresh cache after change table strusture
        // $this->refreshCache();

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
