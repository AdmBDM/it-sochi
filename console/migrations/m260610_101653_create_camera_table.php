<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%camera}}`.
 */
class m260610_101653_create_camera_table extends Migration
{
    /**
     * @return void
     * @throws \yii\base\Exception
     */
    public function safeUp(): void
    {
        $this->createTable('{{%camera}}', [
            'id' => $this->primaryKey(),
            'dvr_id' => $this->integer()->notNull()->comment('ID видеорегистратора'),
            'name' => $this->string(255)->null()->comment('Название камеры'),
            'channel_no' => $this->integer()->null()->comment('Номер канала на DVR'),
            'guid' => $this->string(255)->null()->comment('GUID (для Trassir)'),
            'ip_address' => $this->string(15)->null()->comment('IP-адрес камеры'),
            'mac_address' => $this->string(17)->null()->comment('MAC-адрес'),
            'model' => $this->string(255)->null()->comment('Модель камеры'),
            'firmware' => $this->string(255)->null()->comment('Версия прошивки'),
            'serial_number' => $this->string(255)->null()->comment('Серийный номер'),
            'status' => $this->smallInteger()->notNull()->defaultValue(1)->comment('1-онлайн, 0-офлайн'),
            'stream_main' => $this->string(512)->null()->comment('URL основного потока'),
            'stream_sub' => $this->string(512)->null()->comment('URL дополнительного потока'),
            'resolution' => $this->string(50)->null()->comment('Разрешение'),
            'fps' => $this->integer()->null()->comment('FPS'),
            'codec' => $this->string(50)->null()->comment('Кодек'),
            'ptz_supported' => $this->boolean()->defaultValue(false)->comment('Поддержка PTZ'),
            'ptz_protocol' => $this->string(100)->null()->comment('Протокол PTZ'),
            'location' => $this->string(500)->null()->comment('Расположение/примечание'),
            'extra_data' => $this->json()->null()->comment('Дополнительные данные'),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk_camera_dvr',
            '{{%camera}}',
            'dvr_id',
            '{{%dvr}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex('idx_camera_dvr_id', '{{%camera}}', 'dvr_id');
        $this->createIndex('idx_camera_status', '{{%camera}}', 'status');
        $this->createIndex('idx_camera_ip', '{{%camera}}', 'ip_address');

        $this->execute("
            CREATE TRIGGER trigger_camera_updated_at
            BEFORE UPDATE ON {{%camera}}
            FOR EACH ROW
            EXECUTE FUNCTION update_updated_at_column();
        ");
    }

    /**
     * @return void
     */
    public function safeDown(): void
    {
        $this->execute('DROP TRIGGER IF EXISTS trigger_camera_updated_at ON {{%camera}}');
        $this->dropForeignKey('fk_camera_dvr', '{{%camera}}');
        $this->dropTable('{{%camera}}');
    }
}