<?php

use yii\db\Migration;

class m260710_093615_alter_printer_page_counters extends Migration
{
    /**
     * @return void
     */
    public function safeUp(): void
    {
        $this->addColumn(
            '{{%printer_page_counters}}',
            'created_at',
            $this->timestamp()
                ->notNull()
                ->defaultExpression('CURRENT_TIMESTAMP')
                ->comment('Создано')
        );

        $this->createIndex(
            'idx_printer_page_counters_device_time',
            '{{%printer_page_counters}}',
            ['device_id', 'captured_at']
        );
    }

    /**
     * @return void
     */
    public function safeDown(): void
    {
        $this->dropIndex(
            'idx_printer_page_counters_device_time',
            '{{%printer_page_counters}}'
        );

        $this->dropColumn(
            '{{%printer_page_counters}}',
            'created_at'
        );
    }
}
