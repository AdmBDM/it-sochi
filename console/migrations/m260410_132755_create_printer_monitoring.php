<?php
// console/migrations/m260410_132755_create_printer_monitoring.php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%printer_monitoring}}`.
 */
class m260410_132755_create_printer_monitoring extends Migration
{
    /**
     * @return void
     */
    public function safeUp(): void
    {
        // 1. Метрики принтера в devices
        $this->addColumn('devices', 'printer_metrics', 'JSONB DEFAULT NULL');
        $this->addColumn('devices', 'snmp_ip', 'VARCHAR(15) DEFAULT NULL');
        $this->addColumn('devices', 'snmp_community', "VARCHAR(50) DEFAULT 'public'");
        $this->createIndex('idx_devices_snmp_ip', 'devices', 'snmp_ip');

        // 2. История счётчиков
        $this->createTable('printer_page_counters', [
            'id' => $this->primaryKey(),
            'device_id' => $this->integer()->notNull(),
            'total_pages' => $this->integer()->notNull(),
            'color_pages' => $this->integer()->defaultValue(0),
            'bw_pages' => $this->integer()->defaultValue(0),
            'captured_at' => $this->timestamp()->notNull()->defaultExpression('NOW()'),
            'source' => $this->string(20)->defaultValue('snmp'),
        ]);
        $this->addForeignKey('fk_counters_device', 'printer_page_counters', 'device_id', 'devices', 'id', 'CASCADE');
        $this->createIndex('idx_counters_device_date', 'printer_page_counters', ['device_id', 'captured_at']);

        // 3. Ремонты — parts_replaced через execute() вместо jsonb()
        $this->createTable('printer_repairs', [
            'id' => $this->primaryKey(),
            'device_id' => $this->integer()->notNull(),
            'service_center' => $this->string(255)->notNull(),
            'description' => $this->text()->notNull(),
            'cost' => $this->decimal(10, 2)->defaultValue(0),
            'started_at' => $this->date()->notNull(),
            'finished_at' => $this->date()->null(),
            'document_number' => $this->string(50)->null(),
            'created_at' => $this->timestamp()->defaultExpression('NOW()'),
        ]);

        // JSONB отдельно через execute для PostgreSQL
        $this->execute('ALTER TABLE printer_repairs ADD COLUMN parts_replaced JSONB DEFAULT NULL');

        $this->addForeignKey('fk_repairs_device', 'printer_repairs', 'device_id', 'devices', 'id', 'CASCADE');
        $this->createIndex('idx_repairs_device', 'printer_repairs', 'device_id');
        $this->createIndex('idx_repairs_dates', 'printer_repairs', ['started_at', 'finished_at']);
    }

    /**
     * @return false
     */
    public function safeDown(): false
    {
        $this->dropTable('printer_repairs');
        $this->dropTable('printer_page_counters');

        $this->dropIndex('idx_devices_snmp_ip', 'devices');
        $this->dropColumn('devices', 'snmp_community');
        $this->dropColumn('devices', 'snmp_ip');
        $this->dropColumn('devices', 'printer_metrics');
    }
}
