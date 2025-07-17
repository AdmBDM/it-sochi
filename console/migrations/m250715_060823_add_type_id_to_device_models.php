<?php

use yii\db\Migration;

class m250715_060823_add_type_id_to_device_models extends Migration
{
    /**
     * @return void
     */
    public function safeUp(): void
    {
        $this->addColumn('device_models', 'type_id', $this->integer()->notNull()->defaultValue(1));
        $this->addForeignKey(
            'fk_device_models_type_id',
            'device_models',
            'type_id',
            'device_types',
            'id'
        );
    }

    /**
     * @return void
     */
    public function safeDown(): void
    {
        $this->dropForeignKey('fk_device_models_type_id', 'device_models');
        $this->dropColumn('device_models', 'type_id');
    }
}
