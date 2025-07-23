<?php

use yii\db\Migration;

/**
 * Class m250723_060827_add_is_active_to_tables
 */
class m250723_060827_add_is_active_to_tables extends Migration
{
    /**
     * @return void
     */
    public function safeUp(): void
    {
        $tables = [
            'buildings',
            'cartridge_replacements',
            'cartridge_transfers',
            'cartridge_types',
            'departments',
            'device_brands',
            'device_cartridge_type',
            'device_models',
            'device_statuses',
            'device_types',
            'devices',
            'employees',
            'locations',
            'movements',
            'users',
            'workplaces',
        ];

        foreach ($tables as $table) {
            $this->addColumn($table, 'is_active', $this->boolean()->notNull()->defaultValue(true));
        }
    }

    /**
     * @return void
     */
    public function safeDown(): void
    {
        $tables = [
            'buildings',
            'cartridge_replacements',
            'cartridge_transfers',
            'cartridge_types',
            'departments',
            'device_brands',
            'device_cartridge_type',
            'device_models',
            'device_statuses',
            'device_types',
            'devices',
            'employees',
            'locations',
            'movements',
            'users',
            'workplaces',
        ];

        foreach ($tables as $table) {
            $this->dropColumn($table, 'is_active');
        }
    }
}
