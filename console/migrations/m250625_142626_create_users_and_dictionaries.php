<?php

use yii\db\Migration;

/**
 * Creates users table and equipment dictionaries.
 */
class m250625_142626_create_users_and_dictionaries extends Migration
{
    /**
     * @return void
     */
    public function safeUp()
    {
        // users
        // если таблица users уже существует — переименовываем в users_old
        $tableSchema = Yii::$app->db->schema->getTableSchema('{{%users}}', true);
        if ($tableSchema !== null) {
            $this->renameTable('{{%users}}', '{{%users_old}}');
        }
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(255)->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string(255)->notNull(),
            'password_reset_token' => $this->string(255)->unique(),
            'email' => $this->string(255)->notNull()->unique(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'role' => $this->string(20)->notNull()->defaultValue('guest'),
            'phone' => $this->string(),
            'is_admin' => $this->boolean()->notNull()->defaultValue(false),
            'verification_token' => $this->string(255)->defaultValue(null),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
        ]);
        $this->execute("CREATE TRIGGER trg_set_updated_at_users BEFORE UPDATE ON users FOR EACH ROW EXECUTE FUNCTION set_updated_at();");

        // device_types
        $this->createTable('{{%device_types}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull()->unique(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
        ]);
        $this->execute("CREATE TRIGGER trg_set_updated_at_device_types BEFORE UPDATE ON device_types FOR EACH ROW EXECUTE FUNCTION set_updated_at();");

        // device_brands
        $this->createTable('{{%device_brands}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull()->unique(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
        ]);
        $this->execute("CREATE TRIGGER trg_set_updated_at_device_brands BEFORE UPDATE ON device_brands FOR EACH ROW EXECUTE FUNCTION set_updated_at();");

        // device_models
        $this->createTable('{{%device_models}}', [
            'id' => $this->primaryKey(),
            'brand_id' => $this->integer()->notNull(),
            'name' => $this->string(100)->notNull(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
        ]);
        $this->addForeignKey('fk_device_models_brand', 'device_models', 'brand_id', 'device_brands', 'id', 'CASCADE');
        $this->execute("CREATE TRIGGER trg_set_updated_at_device_models BEFORE UPDATE ON device_models FOR EACH ROW EXECUTE FUNCTION set_updated_at();");

        // device_statuses
        $this->createTable('{{%device_statuses}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull()->unique(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
        ]);
        $this->execute("CREATE TRIGGER trg_set_updated_at_device_statuses BEFORE UPDATE ON device_statuses FOR EACH ROW EXECUTE FUNCTION set_updated_at();");
    }

    /**
     * @return void
     */
    public function safeDown()
    {
        $tables = [
            'device_statuses',
            'device_models',
            'device_brands',
            'device_types',
            'users',
        ];

        foreach ($tables as $table) {
            $this->execute("DROP TRIGGER IF EXISTS trg_set_updated_at_{$table} ON {$table};");
        }

        $this->dropForeignKey('fk_device_models_brand', 'device_models');

        foreach ($tables as $table) {
            $this->dropTable("{{%$table}}");
        }
    }
}
