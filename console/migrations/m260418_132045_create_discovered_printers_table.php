<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%discovered_printers}}`.
 */
class m260418_132045_create_discovered_printers_table extends Migration
{
    /**
     * @return void
     */
    public function safeUp()
    {
        $this->createTable('{{%discovered_printers}}', [
            'id' => $this->primaryKey(),
            'ip' => $this->string(15)->notNull()->unique(),
            'snmp_name' => $this->string(255),
            'snmp_descr' => $this->text(),
            'guessed_model' => $this->string(100),
            'host' => $this->string(100), // для локальных: имя ПК
            'local_name' => $this->string(255), // для локальных: имя принтера в CUPS
            'connection_type' => $this->string(50), // usb, socket, ipp, etc
            'discovered_at' => $this->timestamp(),
            'source' => $this->string(20)->notNull(), // snmp, cups, wmi
            'is_local' => $this->boolean()->defaultValue(false),
            'matched_device_id' => $this->integer(),
            'last_seen_at' => $this->timestamp(),
        ]);
    }

    /**
     * @return false
     */
    public function safeDown(): false
    {
        echo "m260418_132045_create_discovered_printers_table cannot be reverted.\n";
        return false;
    }
}
