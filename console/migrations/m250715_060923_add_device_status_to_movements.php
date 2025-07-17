<?php

use yii\db\Migration;

class m250715_060923_add_device_status_to_movements extends Migration
{
    public function safeUp(): void
    {
        /**
         *
         */
        $this->addColumn('movements', 'device_status_id', $this->integer()->null());
        $this->addForeignKey(
            'fk_movements_device_status_id',
            'movements',
            'device_status_id',
            'device_statuses',
            'id'
        );
    }

    /**
     * @return void
     */
    public function safeDown(): void
    {
        $this->dropForeignKey('fk_movements_device_status_id', 'movements');
        $this->dropColumn('movements', 'device_status_id');
    }
}
