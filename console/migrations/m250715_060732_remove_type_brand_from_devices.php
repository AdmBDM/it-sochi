<?php

use yii\db\Migration;

class m250715_060732_remove_type_brand_from_devices extends Migration
{
    public function safeUp(): void
    {
        // Удаление ограничений, если они есть
        $this->execute('ALTER TABLE devices DROP CONSTRAINT IF EXISTS devices_type_id_fkey');
        $this->execute('ALTER TABLE devices DROP CONSTRAINT IF EXISTS devices_brand_id_fkey');

        // Удаление колонок
        if ($this->db->schema->getTableSchema('devices')->getColumn('type_id')) {
            $this->dropColumn('devices', 'type_id');
        }

        if ($this->db->schema->getTableSchema('devices')->getColumn('brand_id')) {
            $this->dropColumn('devices', 'brand_id');
        }
    }

    public function safeDown(): void
    {
        $this->addColumn('devices', 'type_id', $this->integer());
        $this->addForeignKey(
            'fk_devices_type_id',
            'devices',
            'type_id',
            'device_types',
            'id'
        );

        $this->addColumn('devices', 'brand_id', $this->integer());
        $this->addForeignKey(
            'fk_devices_brand_id',
            'devices',
            'brand_id',
            'device_brands',
            'id'
        );
    }
}
