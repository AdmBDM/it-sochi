<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%worker_schedule}}`.
 */
class m250716_110427_create_worker_schedule_table extends Migration
{
    public function safeUp(): void
    {
        // migration
        $this->createTable('{{%worker_schedule}}', [
            'id' => $this->primaryKey(),
            'worker_name' => $this->string()->notNull(),
            'avatar' => $this->string(),
            'car_number' => $this->string(),
            'car_region' => $this->string(10),
            'car_model' => $this->string(),
            'start_repair' => $this->string(10),
            'finish_repair' => $this->string(10),
            'condition' => $this->string(),
            'created_at' => $this->timestamp()->defaultExpression('NOW()'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
        ]);
        $this->execute("CREATE TRIGGER trg_set_updated_at_worker_schedule BEFORE UPDATE ON worker_schedule FOR EACH ROW EXECUTE FUNCTION set_updated_at();");
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%worker_schedule}}');
    }
}
