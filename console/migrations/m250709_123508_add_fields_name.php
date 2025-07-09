<?php

use yii\db\Migration;

class m250709_123508_add_fields_name extends Migration
{
    /**
     * @return void
     */
    public function safeUp(): void
    {
        $this->addColumn('{{%devices}}', 'name', $this->string(255)->defaultValue(null));
        $this->addColumn('{{%workplaces}}', 'name', $this->string(255)->defaultValue(null));
    }

    /**
     * @return void
     */
    public function safeDown(): void
    {
        $this->dropColumn('{{%devices}}', 'name');
        $this->dropColumn('{{%workplaces}}', 'name');
    }

}
