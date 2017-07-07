<?php

use app\components\Migration;
use yii\db\Schema;

/**
 * Class m170322_074635_AddColumnFromDaemonFlag
 */
class m170322_074635_AddColumnFromDaemonFlag extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%mail}}', 'delivery_failed', Schema::TYPE_INTEGER . '(1) UNSIGNED NOT NULL DEFAULT 0');
        $this->createIndex('idx-mail-delivery_failed', '{{%mail}}', 'delivery_failed');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%mail}}', 'deliveryFailed');
    }
}
