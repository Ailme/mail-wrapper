<?php

namespace app\components;

use yii\db\ColumnSchemaBuilder;
use yii\db\Schema;

/**
 * Class Migration
 * @package app\components
 */
class Migration extends \yii\db\Migration
{
    const TYPE_LONG_TEXT = 'longtext';

    /**
     * @param $name
     * @param $table
     * @param $columns
     */
    public function createFullTextIndex($name, $table, $columns)
    {
        echo '    > create' . " fulltext index $name on $table (" . implode(',', (array) $columns) . ') ...';
        $time = microtime(true);

        $sql = 'CREATE FULLTEXT INDEX '
            . $this->db->quoteTableName($name) . ' ON '
            . $this->db->quoteTableName($table)
            . ' (' . $this->db->getQueryBuilder()->buildColumns($columns) . ')';

        $this->db->createCommand()->setSql($sql)->execute();
        echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    /**
     * Creates a long text column.
     *
     * @return ColumnSchemaBuilder
     */
    public function longText()
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(static::TYPE_LONG_TEXT);
    }
}