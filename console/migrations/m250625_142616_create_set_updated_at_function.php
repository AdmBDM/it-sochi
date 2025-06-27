<?php

use yii\db\Migration;

/**
 * Creates a reusable PostgreSQL trigger function to auto-update "updated_at" fields.
 */
class m250625_142616_create_set_updated_at_function extends Migration
{
    /**
     * @return void
     */
    public function safeUp()
    {
        $this->execute("
            CREATE OR REPLACE FUNCTION set_updated_at()
            RETURNS TRIGGER AS $$
            BEGIN
                NEW.updated_at = now();
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ");
    }

    /**
     * @return void
     */
    public function safeDown()
    {
        $this->execute("DROP FUNCTION IF EXISTS set_updated_at();");
    }
}
