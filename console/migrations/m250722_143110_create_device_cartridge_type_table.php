<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%device_cartridge_type}}`.
 */
class m250722_143110_create_device_cartridge_type_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('device_cartridge_type', [
            'id' => $this->primaryKey(),
            'device_id' => $this->integer()->notNull(),
            'cartridge_type_id' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('NOW()'),
            'updated_at' => $this->timestamp()->defaultExpression('NOW()'),
        ]);

        $this->addForeignKey(
            'fk-device_cartridge_type-device_id',
            'device_cartridge_type',
            'device_id',
            'devices',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-device_cartridge_type-cartridge_type_id',
            'device_cartridge_type',
            'cartridge_type_id',
            'cartridge_types',
            'id',
            'CASCADE'
        );

        $this->execute("CREATE TRIGGER trg_device_cartridge_type_updated_at
            BEFORE UPDATE ON device_cartridge_type
            FOR EACH ROW EXECUTE FUNCTION set_updated_at();");
    }

    public function safeDown(): void
    {
        $this->execute("DROP TRIGGER IF EXISTS trg_device_cartridge_type_updated_at ON device_cartridge_type");
        $this->dropForeignKey('fk-device_cartridge_type-device_id', 'device_cartridge_type');
        $this->dropForeignKey('fk-device_cartridge_type-cartridge_type_id', 'device_cartridge_type');
        $this->dropTable('device_cartridge_type');
    }
}
