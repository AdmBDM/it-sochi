<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%property_definitions}}`.
 */
class m260723_084221_create_property_definitions_table extends Migration
{
    private const string TABLE = '{{%property_definitions}}';

    public function safeUp(): void
    {
        $this->createTable(self::TABLE, [
            'id' => $this->primaryKey(),
            'classifier_id' => $this->integer()->notNull(),
            'code' => $this->string(64)->notNull(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'data_type' => $this->string(32)->notNull(),
            'is_required' => $this->boolean()->notNull()->defaultValue(false),
            'is_multiple' => $this->boolean()->notNull()->defaultValue(false),
            'default_value' => $this->text(),
            'sort_order' => $this->integer()->notNull()->defaultValue(0),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
        ]);

        $this->createIndex(
            'property_definitions_code_key',
            self::TABLE,
            'code',
            true
        );

        $this->createIndex(
            'ix_property_definitions_classifier_id',
            self::TABLE,
            'classifier_id'
        );

        $this->createIndex(
            'ix_property_definitions_name',
            self::TABLE,
            'name'
        );

        $this->createIndex(
            'ix_property_definitions_data_type',
            self::TABLE,
            'data_type'
        );

        $this->createIndex(
            'ix_property_definitions_sort_order',
            self::TABLE,
            'sort_order'
        );

        $this->createIndex(
            'ix_property_definitions_is_active',
            self::TABLE,
            'is_active'
        );

        $this->addForeignKey(
            'fk_property_definitions_classifier_id',
            self::TABLE,
            'classifier_id',
            '{{%classifiers}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->execute('
            CREATE TRIGGER trg_property_definitions_updated_at
            BEFORE UPDATE ON {{%property_definitions}}
            FOR EACH ROW
            EXECUTE FUNCTION update_updated_at_column();
        ');
    }

    public function safeDown(): void
    {
        $this->execute('
            DROP TRIGGER IF EXISTS trg_property_definitions_updated_at
            ON {{%property_definitions}};
        ');

        $this->dropForeignKey(
            'fk_property_definitions_classifier_id',
            self::TABLE
        );

        $this->dropIndex(
            'ix_property_definitions_is_active',
            self::TABLE
        );

        $this->dropIndex(
            'ix_property_definitions_sort_order',
            self::TABLE
        );

        $this->dropIndex(
            'ix_property_definitions_data_type',
            self::TABLE
        );

        $this->dropIndex(
            'ix_property_definitions_name',
            self::TABLE
        );

        $this->dropIndex(
            'ix_property_definitions_classifier_id',
            self::TABLE
        );

        $this->dropIndex(
            'property_definitions_code_key',
            self::TABLE
        );

        $this->dropTable(self::TABLE);
    }
}
