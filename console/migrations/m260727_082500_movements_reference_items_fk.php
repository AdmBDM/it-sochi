<?php

use yii\db\Migration;

class m260727_082500_movements_reference_items_fk extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $movementsTable = '{{%movements}}';
        $referenceItemsTable = '{{%reference_items}}';

        $this->addColumn($movementsTable, 'device_status_reference_id', $this->integer()->null());

        $this->createIndex(
            'idx-movements-device_status_reference_id',
            $movementsTable,
            'device_status_reference_id'
        );

        $this->addForeignKey(
            'fk-movements-device_status_reference_id',
            $movementsTable,
            'device_status_reference_id',
            $referenceItemsTable,
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->execute("
            UPDATE {$movementsTable} m
               SET device_status_reference_id = ri.id
              FROM {$referenceItemsTable} ri
             WHERE ri.legacy_table = 'device_statuses'
               AND ri.legacy_id = m.device_status_id
        ");

        $notMapped = (new \yii\db\Query())
            ->from($movementsTable)
            ->where(['device_status_reference_id' => null])
            ->count();

        if ((int)$notMapped > 0) {
            throw new \RuntimeException(
                'Не удалось заполнить device_status_reference_id для всех записей таблицы movements.'
            );
        }

        $this->alterColumn(
            $movementsTable,
            'device_status_reference_id',
            $this->integer()->notNull()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $movementsTable = '{{%movements}}';

        $this->dropForeignKey(
            'fk-movements-device_status_reference_id',
            $movementsTable
        );

        $this->dropIndex(
            'idx-movements-device_status_reference_id',
            $movementsTable
        );

        $this->dropColumn(
            $movementsTable,
            'device_status_reference_id'
        );
    }
}
