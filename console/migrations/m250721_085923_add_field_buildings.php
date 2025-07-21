<?php

use yii\db\Migration;

class m250721_085923_add_field_buildings extends Migration
{
    /**
     * @return void
     */
    public function safeUp(): void
    {
        $this->addColumn('{{%buildings}}', 'small', $this->string());
    }

    /**
     * @return false
     */
    public function safeDown(): false
    {
        echo "m250721_085923_add_field_buildings cannot be reverted.\n";
        return false;
    }
}
