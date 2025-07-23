<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%cartridge_types}}`.
 */
class m250722_143048_create_cartridge_types_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('cartridge_types', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique(),
            'color' => $this->string(50),
            'initial_quantity' => $this->integer()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('NOW()'),
            'updated_at' => $this->timestamp()->defaultExpression('NOW()'),
        ]);

        $this->execute("CREATE TRIGGER trg_cartridge_types_updated_at
            BEFORE UPDATE ON cartridge_types
            FOR EACH ROW EXECUTE FUNCTION set_updated_at();");
    }

    public function safeDown(): void
    {
        $this->execute("DROP TRIGGER IF EXISTS trg_cartridge_types_updated_at ON cartridge_types");
        $this->dropTable('cartridge_types');
    }
}
