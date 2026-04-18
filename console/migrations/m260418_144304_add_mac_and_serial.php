<?php

use yii\db\Migration;

class m260418_144304_add_mac_and_serial extends Migration
{
    /**
     * @return void
     */
    public function safeUp(): void
    {
        $this->addColumn('discovered_printers', 'mac_address', 'VARCHAR(17)');
        $this->addColumn('discovered_printers', 'serial_snmp', 'VARCHAR(100)');
        $this->addColumn('discovered_printers', 'last_ip', 'VARCHAR(15)');

        $this->createIndex('idx_discovered_mac', 'discovered_printers', 'mac_address');
        $this->createIndex('idx_discovered_serial', 'discovered_printers', 'serial_snmp');
    }

    /**
     * @return void
     */
    public function safeDown(): void
    {
        $this->dropColumn('discovered_printers', 'mac_address');
        $this->dropColumn('discovered_printers', 'serial_snmp');
        $this->dropColumn('discovered_printers', 'last_ip');
    }
}
