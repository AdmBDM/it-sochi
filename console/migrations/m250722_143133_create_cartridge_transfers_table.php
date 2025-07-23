<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%cartridge_transfers}}`.
 */
class m250722_143133_create_cartridge_transfers_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('cartridge_transfers', [
            'id' => $this->primaryKey(),
            'type' => "VARCHAR(10) CHECK (type IN ('shipment', 'return')) NOT NULL",
            'code' => $this->string(50)->notNull()->unique(),
            'created_at' => $this->timestamp()->defaultExpression('NOW()'),
            'updated_at' => $this->timestamp()->defaultExpression('NOW()'),
        ]);

        $this->execute("CREATE TRIGGER trg_cartridge_transfers_updated_at
            BEFORE UPDATE ON cartridge_transfers
            FOR EACH ROW EXECUTE FUNCTION set_updated_at();");
    }

    public function safeDown(): void
    {
        $this->execute("DROP TRIGGER IF EXISTS trg_cartridge_transfers_updated_at ON cartridge_transfers");
        $this->dropTable('cartridge_transfers');
    }
}
