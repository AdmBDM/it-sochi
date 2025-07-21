<?php

use yii\db\Migration;

class m250721_081702_add_fields_employees extends Migration
{
    /**
     * @return void
     */
    public function safeUp(): void
    {
        $this->addColumn('{{%employees}}', 'first_name', $this->string());
        $this->addColumn('{{%employees}}', 'middle_name', $this->string());
        $this->addColumn('{{%employees}}', 'last_name', $this->string());
    }

    /**
     * @return false
     */
    public function safeDown(): false
    {
        echo "m250721_081702_add_fields_employees cannot be reverted.\n";

        return false;
    }
}
