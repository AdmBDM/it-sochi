<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%catalog_items}}`.
 */
class m260723_075139_create_catalog_items_table extends Migration
{
    private const string TABLE = '{{%catalog_items}}';

    public function safeUp(): void
    {
        $this->createTable(self::TABLE, [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer(),
            'vendor_id' => $this->integer()->notNull(),
            'code' => $this->string(64)->notNull(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'sort_order' => $this->integer()->notNull()->defaultValue(0),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
        ]);

        $this->createIndex(
            'catalog_items_code_key',
            self::TABLE,
            'code',
            true
        );

        $this->createIndex(
            'ix_catalog_items_parent_id',
            self::TABLE,
            'parent_id'
        );

        $this->createIndex(
            'ix_catalog_items_vendor_id',
            self::TABLE,
            'vendor_id'
        );

        $this->createIndex(
            'ix_catalog_items_name',
            self::TABLE,
            'name'
        );

        $this->createIndex(
            'ix_catalog_items_sort_order',
            self::TABLE,
            'sort_order'
        );

        $this->createIndex(
            'ix_catalog_items_is_active',
            self::TABLE,
            'is_active'
        );

        $this->addForeignKey(
            'fk_catalog_items_parent_id',
            self::TABLE,
            'parent_id',
            self::TABLE,
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_catalog_items_vendor_id',
            self::TABLE,
            'vendor_id',
            '{{%vendors}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->execute('
            CREATE TRIGGER trg_catalog_items_updated_at
            BEFORE UPDATE ON {{%catalog_items}}
            FOR EACH ROW
            EXECUTE FUNCTION update_updated_at_column();
        ');
    }

    public function safeDown(): void
    {
        $this->execute('
            DROP TRIGGER IF EXISTS trg_catalog_items_updated_at
            ON {{%catalog_items}};
        ');

        $this->dropForeignKey(
            'fk_catalog_items_vendor_id',
            self::TABLE
        );

        $this->dropForeignKey(
            'fk_catalog_items_parent_id',
            self::TABLE
        );

        $this->dropIndex(
            'ix_catalog_items_is_active',
            self::TABLE
        );

        $this->dropIndex(
            'ix_catalog_items_sort_order',
            self::TABLE
        );

        $this->dropIndex(
            'ix_catalog_items_name',
            self::TABLE
        );

        $this->dropIndex(
            'ix_catalog_items_vendor_id',
            self::TABLE
        );

        $this->dropIndex(
            'ix_catalog_items_parent_id',
            self::TABLE
        );

        $this->dropIndex(
            'catalog_items_code_key',
            self::TABLE
        );

        $this->dropTable(self::TABLE);
    }
}
