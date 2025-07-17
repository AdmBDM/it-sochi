<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%display_config}}`.
 */
class m250716_134331_create_display_config_table extends Migration
{
    /**
     * @return void
     */
    public function safeUp(): void
    {
        $this->createTable('{{%display_config}}', [
            'id' => $this->primaryKey(),
            'screen_id' => $this->string()->notNull()->unique(),
            'layout_order' => $this->text()->notNull(),   // JSON список блоков
            'duration_map' => $this->text()->null(),      // JSON: { "schedule": 60, "html": 20 }
            'enabled' => $this->boolean()->defaultValue(true),
            'created_at' => $this->timestamp()->defaultExpression('NOW()'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
        ]);

        $this->execute("CREATE TRIGGER trg_set_updated_at_display_config BEFORE UPDATE ON display_config FOR EACH ROW EXECUTE FUNCTION set_updated_at();");
    }

    /**
     * @return void
     */
    public function safeDown(): void
    {
        $this->dropTable('{{%display_config}}');
    }
}
