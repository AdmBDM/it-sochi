<?php

declare(strict_types=1);

use yii\db\Migration;

class m260722_082119_create_classifiers_table extends Migration
{
    private const string TABLE = '{{%classifiers}}';

    public function safeUp(): void
    {
        $this->createTable(self::TABLE, [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer(),
            'code' => $this->string(64)->notNull(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'sort_order' => $this->integer()->notNull()->defaultValue(0),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
        ]);

        // UNIQUE(code)
        $this->createIndex(
            'classifiers_code_key',
            self::TABLE,
            'code',
            true
        );

        $this->createIndex(
            'ix_classifiers_parent_id',
            self::TABLE,
            'parent_id'
        );

        $this->createIndex(
            'ix_classifiers_name',
            self::TABLE,
            'name'
        );

        $this->createIndex(
            'ix_classifiers_sort_order',
            self::TABLE,
            'sort_order'
        );

        $this->createIndex(
            'ix_classifiers_is_active',
            self::TABLE,
            'is_active'
        );

        $this->addForeignKey(
            'fk_classifiers_parent_id',
            self::TABLE,
            'parent_id',
            '{{%classifiers}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->execute("
            CREATE TRIGGER trg_classifiers_updated_at
            BEFORE UPDATE ON " . self::TABLE . "
            FOR EACH ROW
            EXECUTE FUNCTION update_updated_at_column();
        ");
    }

    public function safeDown(): void
    {
        $this->execute(
            'DROP TRIGGER IF EXISTS trg_classifiers_updated_at ON {{%classifiers}};'
        );

        $this->dropForeignKey(
            'fk_classifiers_parent_id',
            self::TABLE,
        );

        $this->dropIndex(
            'ix_classifiers_is_active',
            self::TABLE,
        );

        $this->dropIndex(
            'ix_classifiers_sort_order',
            self::TABLE,
        );

        $this->dropIndex(
            'ix_classifiers_name',
            self::TABLE,
        );

        $this->dropIndex(
            'ix_classifiers_parent_id',
            self::TABLE,
        );

        $this->dropIndex(
            'classifiers_code_key',
            self::TABLE,
        );

        $this->dropTable(self::TABLE );
    }
}
