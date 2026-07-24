<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%reference_items}}`.
 */
class m260724_054826_create_reference_items_table extends Migration
{
    private const string TABLE = '{{%reference_items}}';

    /**
     * @return void
     */
    public function safeUp(): void
    {
        $this->createTable(self::TABLE, [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer()->null(),
            'type_id' => $this->integer()->null(),
            'code' => $this->string(100)->notNull(),
            'name' => $this->string()->notNull(),
            'description' => $this->text(),
            'sort_order' => $this->integer()->notNull()->defaultValue(0),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'is_deleted' => $this->boolean()->notNull()->defaultValue(false),
            'legacy_table' => $this->string(100),
            'legacy_id' => $this->integer(),
            'created_at' => 'timestamp NOT NULL DEFAULT now()',
            'updated_at' => 'timestamp NOT NULL DEFAULT now()',
        ]);

        $this->createIndex(
            'idx-reference_items-parent_id',
            self::TABLE,
            'parent_id'
        );

        $this->createIndex(
            'idx-reference_items-type_id',
            self::TABLE,
            'type_id'
        );

        $this->createIndex(
            'ux-reference_items-code',
            self::TABLE,
            'code',
            true
        );

        $this->createIndex(
            'idx-reference_items-is_active',
            self::TABLE,
            'is_active'
        );

        $this->createIndex(
            'idx-reference_items-is_deleted',
            self::TABLE,
            'is_deleted'
        );

        $this->createIndex(
            'idx-reference_items-sort_order',
            self::TABLE,
            'sort_order'
        );

        $this->addForeignKey(
            'fk-reference_items-parent_id',
            self::TABLE,
            'parent_id',
            self::TABLE,
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-reference_items-type_id',
            self::TABLE,
            'type_id',
            self::TABLE,
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->execute("
            CREATE TRIGGER trg_reference_items_updated_at
            BEFORE UPDATE ON reference_items
            FOR EACH ROW
            EXECUTE FUNCTION update_updated_at_column();
        ");
    }

    /**
     * @return void
     */
    public function safeDown(): void
    {
        $this->execute("
            DROP TRIGGER IF EXISTS trg_reference_items_updated_at
            ON reference_items;
        ");

        $this->dropForeignKey(
            'fk-reference_items-type_id',
            self::TABLE
        );

        $this->dropForeignKey(
            'fk-reference_items-parent_id',
            self::TABLE
        );

        $this->dropIndex(
            'idx-reference_items-sort_order',
            self::TABLE
        );

        $this->dropIndex(
            'idx-reference_items-is_deleted',
            self::TABLE
        );

        $this->dropIndex(
            'idx-reference_items-is_active',
            self::TABLE
        );

        $this->dropIndex(
            'ux-reference_items-code',
            self::TABLE
        );

        $this->dropIndex(
            'idx-reference_items-type_id',
            self::TABLE
        );

        $this->dropIndex(
            'idx-reference_items-parent_id',
            self::TABLE
        );

        $this->dropTable(self::TABLE);
    }
}
