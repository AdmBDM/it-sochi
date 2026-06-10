<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%dvr}}`.
 */
class m260610_101625_create_dvr_table extends Migration
{
    /**
     * @return void
     * @throws \yii\base\Exception
     */
    public function safeUp(): void
    {
        $this->createTable('{{%dvr}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull()->comment('Название блока/объекта'),
            'system_type' => $this->string(50)->notNull()->comment('Тип системы: trassir, dahua, hikvision'),
            'ip_address' => $this->string(15)->notNull()->comment('IP видеорегистратора'),
            'port' => $this->integer()->notNull()->comment('Порт управления'),
            'network' => $this->string(18)->notNull()->comment('Подсеть камер, например 192.168.124.0/24'),
            'username' => $this->string(100)->null()->comment('Логин для API'),
            'password' => $this->string(255)->null()->comment('Пароль для API'),
            'sdk_password' => $this->string(255)->null()->comment('SDK-пароль (для Trassir)'),
            'status' => $this->smallInteger()->notNull()->defaultValue(1)->comment('1-активен, 0-неактивен'),
            'last_scan_at' => $this->timestamp()->null()->comment('Время последнего опроса'),
            'model' => $this->string(255)->null()->comment('Модель устройства'),
            'firmware' => $this->string(255)->null()->comment('Версия прошивки'),
            'serial_number' => $this->string(255)->null()->comment('Серийный номер'),
            'mac_address' => $this->string(17)->null()->comment('MAC-адрес'),
            'channel_count' => $this->integer()->null()->comment('Количество каналов'),
            'disk_info' => $this->json()->null()->comment('Информация о дисках'),
            'extra_data' => $this->json()->null()->comment('Дополнительные данные от API'),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx_dvr_system_type', '{{%dvr}}', 'system_type');
        $this->createIndex('idx_dvr_ip', '{{%dvr}}', 'ip_address');
        $this->createIndex('idx_dvr_status', '{{%dvr}}', 'status');

        // Триггер для updated_at
        $this->execute("
            CREATE OR REPLACE FUNCTION update_updated_at_column()
            RETURNS TRIGGER AS $$
            BEGIN
                NEW.updated_at = CURRENT_TIMESTAMP;
                RETURN NEW;
            END;
            $$ language 'plpgsql';
        ");

        $this->execute("
            CREATE TRIGGER trigger_dvr_updated_at
            BEFORE UPDATE ON {{%dvr}}
            FOR EACH ROW
            EXECUTE FUNCTION update_updated_at_column();
        ");
    }

    /**
     * @return void
     */
    public function safeDown(): void
    {
        $this->execute('DROP TRIGGER IF EXISTS trigger_dvr_updated_at ON {{%dvr}}');
        $this->dropTable('{{%dvr}}');
    }
}