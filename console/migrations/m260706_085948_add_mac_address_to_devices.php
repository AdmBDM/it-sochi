<?php

use yii\db\Migration;

class m260706_085948_add_mac_address_to_devices extends Migration
{
    /**
     * @return void
     */
    public function safeUp(): void
    {
        {
            $this->addColumn('devices', 'mac_address', $this->string(17)->null()->comment('MAC-address'));
            $this->createIndex('idx_devices_mac_address', 'devices', 'mac_address');
        }
    }

    /**
     * @return void
     */
    public function safeDown(): void
    {
        $this->dropIndex('idx_devices_mac_address', 'devices');
        $this->dropColumn('devices', 'mac_address');
    }
}
