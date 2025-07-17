<?php

use yii\db\Migration;

class m250716_142559_add_source_id_to_worker_schedule extends Migration
{
    /**
     * @return void
     */
    public function safeUp(): void
    {
        $this->addColumn('{{%worker_schedule}}', 'source_id', $this->string(64)->notNull()->defaultValue('unknown'));
        $this->createIndex('idx_worker_schedule_source_id', '{{%worker_schedule}}', 'source_id');
    }

    /**
     * @return false
     */
    public function safeDown(): false
    {
        echo "m250716_142559_add_source_id_to_worker_schedule cannot be reverted.\n";
        return false;
    }
}
