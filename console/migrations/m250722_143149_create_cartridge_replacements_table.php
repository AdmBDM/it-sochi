<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%cartridge_replacements}}`.
 */
class m250722_143149_create_cartridge_replacements_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('cartridge_replacements', [
            'id' => $this->primaryKey(),
            'device_id' => $this->integer()->notNull(),
            'old_cartridge_type_id' => $this->integer(),
            'new_cartridge_type_id' => $this->integer()->notNull(),
            'replaced_at' => $this->timestamp()->defaultExpression('NOW()'),
            'shipment_id' => $this->integer(),
            'return_id' => $this->integer(),
            'created_at' => $this->timestamp()->defaultExpression('NOW()'),
            'updated_at' => $this->timestamp()->defaultExpression('NOW()'),
        ]);

        $this->addForeignKey(
            'fk-cartridge_replacements-device_id',
            'cartridge_replacements',
            'device_id',
            'devices',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-cartridge_replacements-old_cartridge_type_id',
            'cartridge_replacements',
            'old_cartridge_type_id',
            'cartridge_types',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk-cartridge_replacements-new_cartridge_type_id',
            'cartridge_replacements',
            'new_cartridge_type_id',
            'cartridge_types',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-cartridge_replacements-shipment_id',
            'cartridge_replacements',
            'shipment_id',
            'cartridge_transfers',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk-cartridge_replacements-return_id',
            'cartridge_replacements',
            'return_id',
            'cartridge_transfers',
            'id',
            'SET NULL'
        );

        $this->execute("CREATE TRIGGER trg_cartridge_replacements_updated_at
            BEFORE UPDATE ON cartridge_replacements
            FOR EACH ROW EXECUTE FUNCTION set_updated_at();");
    }

    public function safeDown(): void
    {
        $this->execute("DROP TRIGGER IF EXISTS trg_cartridge_replacements_updated_at ON cartridge_replacements");
        $this->dropForeignKey('fk-cartridge_replacements-device_id', 'cartridge_replacements');
        $this->dropForeignKey('fk-cartridge_replacements-old_cartridge_type_id', 'cartridge_replacements');
        $this->dropForeignKey('fk-cartridge_replacements-new_cartridge_type_id', 'cartridge_replacements');
        $this->dropForeignKey('fk-cartridge_replacements-shipment_id', 'cartridge_replacements');
        $this->dropForeignKey('fk-cartridge_replacements-return_id', 'cartridge_replacements');
        $this->dropTable('cartridge_replacements');
    }
}
