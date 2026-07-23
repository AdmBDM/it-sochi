<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%organization_objects}}`.
 */
class m260723_085817_create_organization_objects_table extends Migration
{
    private const string TABLE = '{{%organization_objects}}';

    public function safeUp(): void
    {
        $this->createTable(self::TABLE, [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer(),
            'classifier_id' => $this->integer()->notNull(),
            'code' => $this->string(64)->notNull(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'sort_order' => $this->integer()->notNull()->defaultValue(0),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
        ]);

        $this->createIndex(
            'organization_objects_code_key',
            self::TABLE,
            'code',
            true
        );

        $this->createIndex(
            'ix_organization_objects_parent_id',
            self::TABLE,
            'parent_id'
        );

        $this->createIndex(
            'ix_organization_objects_classifier_id',
            self::TABLE,
            'classifier_id'
        );

        $this->createIndex(
            'ix_organization_objects_name',
            self::TABLE,
            'name'
        );

        $this->createIndex(
            'ix_organization_objects_sort_order',
            self::TABLE,
            'sort_order'
        );

        $this->createIndex(
            'ix_organization_objects_is_active',
            self::TABLE,
            'is_active'
        );

        $this->addForeignKey(
            'fk_organization_objects_parent_id',
            self::TABLE,
            'parent_id',
            self::TABLE,
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_organization_objects_classifier_id',
            self::TABLE,
            'classifier_id',
            '{{%classifiers}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->execute('
            CREATE TRIGGER trg_organization_objects_updated_at
            BEFORE UPDATE ON {{%organization_objects}}
            FOR EACH ROW
            EXECUTE FUNCTION update_updated_at_column();
        ');
    }

    public function safeDown(): void
    {
        $this->execute('
            DROP TRIGGER IF EXISTS trg_organization_objects_updated_at
            ON {{%organization_objects}};
        ');

        $this->dropForeignKey(
            'fk_organization_objects_classifier_id',
            self::TABLE
        );

        $this->dropForeignKey(
            'fk_organization_objects_parent_id',
            self::TABLE
        );

        $this->dropIndex(
            'ix_organization_objects_is_active',
            self::TABLE
        );

        $this->dropIndex(
            'ix_organization_objects_sort_order',
            self::TABLE
        );

        $this->dropIndex(
            'ix_organization_objects_name',
            self::TABLE
        );

        $this->dropIndex(
            'ix_organization_objects_classifier_id',
            self::TABLE
        );

        $this->dropIndex(
            'ix_organization_objects_parent_id',
            self::TABLE
        );

        $this->dropIndex(
            'organization_objects_code_key',
            self::TABLE
        );

        $this->dropTable(self::TABLE);
    }
}
