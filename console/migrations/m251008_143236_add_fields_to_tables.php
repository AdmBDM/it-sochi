<?php

use yii\db\Migration;

class m251008_143236_add_fields_to_tables extends Migration
{
    /**
     * @return void
     */
    public function safeUp(): void
    {
        // 1. Тип изменения: "status" или "place"
        $this->addColumn('movements', 'type_change', $this->string()->notNull());
        $this->addCommentOnColumn('movements', 'type_change', 'Тип изменения: status или place');

        // 2. Старый ID статуса или рабочего места
        $this->addColumn('movements', 'id_old', $this->integer());
        $this->addCommentOnColumn('movements', 'id_old', 'Старый ID статуса или рабочего места');

        // 3. Новый ID статуса или рабочего места
        $this->addColumn('movements', 'id_new', $this->integer());
        $this->addCommentOnColumn('movements', 'id_new', 'Новый ID статуса или рабочего места');

        // (опционально) ограничение допустимых значений
        $this->execute(
            "ALTER TABLE movements ADD CONSTRAINT chk_movements_type_change
             CHECK (type_change IN ('status', 'place'))"
        );
    }

    /**
     * @return void
     */
    public function safeDown(): void
    {
        $this->execute("ALTER TABLE movements DROP CONSTRAINT IF EXISTS chk_movements_type_change");
        $this->dropCommentFromColumn('movements', 'type_change');
        $this->dropCommentFromColumn('movements', 'id_old');
        $this->dropCommentFromColumn('movements', 'id_new');

        $this->dropColumn('movements', 'type_change');
        $this->dropColumn('movements', 'id_old');
        $this->dropColumn('movements', 'id_new');
    }
}
