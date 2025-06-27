<?php

use yii\db\Migration;

/**
 * Creates employees, workplaces, devices, and movements.
 */
class m250625_142646_create_inventory_core_tables extends Migration
{
    /**
     * @return void
     */
    public function safeUp()
    {
        // employees
        $this->createTable('{{%employees}}', [
            'id' => $this->primaryKey(),
            'full_name' => $this->string(255)->notNull(),
            'email' => $this->string(255),
            'phone' => $this->string(50),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
        ]);
        $this->execute("CREATE TRIGGER trg_set_updated_at_employees BEFORE UPDATE ON employees FOR EACH ROW EXECUTE FUNCTION set_updated_at();");

        // workplaces
        $this->createTable('{{%workplaces}}', [
            'id' => $this->primaryKey(),
            'employee_id' => $this->integer()->notNull(),
            'location_id' => $this->integer()->notNull(),
            'department_id' => $this->integer()->notNull(),
            'comment' => $this->text(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
        ]);
        $this->addForeignKey('fk_workplaces_employee', 'workplaces', 'employee_id', 'employees', 'id', 'CASCADE');
        $this->addForeignKey('fk_workplaces_location', 'workplaces', 'location_id', 'locations', 'id', 'CASCADE');
        $this->addForeignKey('fk_workplaces_department', 'workplaces', 'department_id', 'departments', 'id', 'CASCADE');
        $this->execute("CREATE TRIGGER trg_set_updated_at_workplaces BEFORE UPDATE ON workplaces FOR EACH ROW EXECUTE FUNCTION set_updated_at();");

        // devices
        $this->createTable('{{%devices}}', [
            'id' => $this->primaryKey(),
            'workplace_id' => $this->integer()->notNull(),
            'type_id' => $this->integer()->notNull(),
            'brand_id' => $this->integer()->notNull(),
            'model_id' => $this->integer()->notNull(),
            'status_id' => $this->integer()->notNull(),
            'serial_number' => $this->string(100),
            'inventory_number' => $this->string(100),
            'comment' => $this->text(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
        ]);
        $this->addForeignKey('fk_devices_workplace', 'devices', 'workplace_id', 'workplaces', 'id', 'CASCADE');
        $this->addForeignKey('fk_devices_type', 'devices', 'type_id', 'device_types', 'id', 'RESTRICT');
        $this->addForeignKey('fk_devices_brand', 'devices', 'brand_id', 'device_brands', 'id', 'RESTRICT');
        $this->addForeignKey('fk_devices_model', 'devices', 'model_id', 'device_models', 'id', 'RESTRICT');
        $this->addForeignKey('fk_devices_status', 'devices', 'status_id', 'device_statuses', 'id', 'RESTRICT');
        $this->execute("CREATE TRIGGER trg_set_updated_at_devices BEFORE UPDATE ON devices FOR EACH ROW EXECUTE FUNCTION set_updated_at();");

        // movements
        $this->createTable('{{%movements}}', [
            'id' => $this->primaryKey(),
            'device_id' => $this->integer()->notNull(),
            'from_workplace_id' => $this->integer(),
            'to_workplace_id' => $this->integer()->notNull(),
            'moved_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
            'moved_by_user_id' => $this->integer()->notNull(),
            'comment' => $this->text(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
        ]);
        $this->addForeignKey('fk_movements_device', 'movements', 'device_id', 'devices', 'id', 'CASCADE');
        $this->addForeignKey('fk_movements_from', 'movements', 'from_workplace_id', 'workplaces', 'id', 'SET NULL');
        $this->addForeignKey('fk_movements_to', 'movements', 'to_workplace_id', 'workplaces', 'id', 'CASCADE');
        $this->addForeignKey('fk_movements_user', 'movements', 'moved_by_user_id', 'users', 'id', 'RESTRICT');
        $this->execute("CREATE TRIGGER trg_set_updated_at_movements BEFORE UPDATE ON movements FOR EACH ROW EXECUTE FUNCTION set_updated_at();");
    }

    /**
     * @return void
     */
    public function safeDown()
    {
        $tables = [
            'movements',
            'devices',
            'workplaces',
            'employees',
        ];

        foreach ($tables as $table) {
            $this->execute("DROP TRIGGER IF EXISTS trg_set_updated_at_{$table} ON {$table};");
        }

        $this->dropForeignKey('fk_movements_user', 'movements');
        $this->dropForeignKey('fk_movements_to', 'movements');
        $this->dropForeignKey('fk_movements_from', 'movements');
        $this->dropForeignKey('fk_movements_device', 'movements');

        $this->dropForeignKey('fk_devices_status', 'devices');
        $this->dropForeignKey('fk_devices_model', 'devices');
        $this->dropForeignKey('fk_devices_brand', 'devices');
        $this->dropForeignKey('fk_devices_type', 'devices');
        $this->dropForeignKey('fk_devices_workplace', 'devices');

        $this->dropForeignKey('fk_workplaces_department', 'workplaces');
        $this->dropForeignKey('fk_workplaces_location', 'workplaces');
        $this->dropForeignKey('fk_workplaces_employee', 'workplaces');

        foreach ($tables as $table) {
            $this->dropTable("{{%$table}}");
        }
    }
}
