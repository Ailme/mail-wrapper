<?php

use app\components\Migration;

/**
 * Handles the creation of table `{{%mail}}`.
 */
class m170309_144355_create_mail_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        }

        $this->createTable('{{%mail}}', [
            'id' => $this->primaryKey(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
            'type' => $this->string(3)->notNull()->defaultValue('in'),
            'raw' => $this->longText()->notNull(),
            'from' => $this->string(255)->notNull(),
            'to' => $this->text()->notNull(),
            'subject' => $this->string(1000)->notNull(),
            'text' => $this->longText()->notNull(),
            'html' => $this->longText()->notNull(),
            'files' => $this->text()->notNull(),
            'xml' => $this->longText()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-mail-created_at', '{{%mail}}', 'created_at');
        $this->createIndex('idx-mail-type', '{{%mail}}', 'type');
        $this->createFullTextIndex('idx-mail-raw', '{{%mail}}', 'raw');
        $this->createIndex('idx-mail-from', '{{%mail}}', 'from');
        $this->createFullTextIndex('idx-mail-to', '{{%mail}}', 'to');
        $this->createIndex('idx-mail-subject', '{{%mail}}', 'subject');
        $this->createFullTextIndex('idx-mail-text', '{{%mail}}', 'text');
        $this->createFullTextIndex('idx-mail-html', '{{%mail}}', 'html');
        $this->createFullTextIndex('idx-mail-files', '{{%mail}}', 'files');
        $this->createFullTextIndex('idx-mail-xml', '{{%mail}}', 'xml');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%mail}}');
    }
}
