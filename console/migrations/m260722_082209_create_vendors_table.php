<?php

declare(strict_types=1);

use yii\db\Migration;

class m260722_082209_create_vendors_table extends Migration
{
    private const string TABLE = '{{%vendors}}';

    public function safeUp(): void
    {
        $this->createTable(self::TABLE, [
            'id' => $this->primaryKey(),
            'code' => $this->string(64)->notNull(),
            'name' => $this->string(255)->notNull(),
            'website' => $this->string(255),
            'description' => $this->text(),
            'sort_order' => $this->integer()->notNull()->defaultValue(0),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
        ]);

        $this->createIndex(
            'vendors_code_key',
            self::TABLE,
            'code',
            true
        );

        $this->createIndex(
            'vendors_name_key',
            self::TABLE,
            'name',
            true
        );

        $this->createIndex(
            'ix_vendors_sort_order',
            self::TABLE,
            'sort_order'
        );

        $this->createIndex(
            'ix_vendors_is_active',
            self::TABLE,
            'is_active'
        );

        $this->execute('
            CREATE TRIGGER trg_vendors_updated_at
            BEFORE UPDATE ON {{%vendors}}
            FOR EACH ROW
            EXECUTE FUNCTION update_updated_at_column();
        ');
    }

    public function safeDown(): void
    {
        $this->execute('
            DROP TRIGGER IF EXISTS trg_vendors_updated_at ON {{%vendors}};
        ');

        $this->dropIndex(
            'ix_vendors_is_active',
            self::TABLE
        );

        $this->dropIndex(
            'ix_vendors_sort_order',
            self::TABLE
        );

        $this->dropIndex(
            'vendors_name_key',
            self::TABLE
        );

        $this->dropIndex(
            'vendors_code_key',
            self::TABLE
        );

        $this->dropTable(self::TABLE);
    }
}
