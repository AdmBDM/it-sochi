<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%scan_log}}`.
 */
class m260610_101746_create_scan_log_table extends Migration
{
    /**
     * @return void
     * @throws \yii\base\Exception
     */
    public function safeUp(): void
    {
        $this->createTable('{{%scan_log}}', [
            'id' => $this->primaryKey(),
            'dvr_id' => $this->integer()->null()->comment('ID DVR (null = все)'),
            'scan_type' => $this->string(50)->notNull()->comment('dvr_scan или camera_scan'),
            'status' => $this->string(20)->notNull()->comment('success, error, partial'),
            'message' => $this->text()->null()->comment('Сообщение/ошибка'),
            'details' => $this->json()->null()->comment('Детали сканирования'),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx_scan_log_dvr', '{{%scan_log}}', 'dvr_id');
        $this->createIndex('idx_scan_log_type', '{{%scan_log}}', 'scan_type');

        $this->execute("
            CREATE TRIGGER trigger_scan_log_updated_at
            BEFORE UPDATE ON {{%scan_log}}
            FOR EACH ROW
            EXECUTE FUNCTION update_updated_at_column();
        ");
    }

    /**
     * @return void
     */
    public function safeDown(): void
    {
        $this->execute('DROP TRIGGER IF EXISTS trigger_scan_log_updated_at ON {{%scan_log}}');
        $this->dropTable('{{%scan_log}}');
    }
}
