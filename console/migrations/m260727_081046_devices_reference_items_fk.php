<?php

use yii\db\Migration;

class m260727_081046_devices_reference_items_fk extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $devicesTable = '{{%devices}}';
        $referenceItemsTable = '{{%reference_items}}';

        $this->addColumn($devicesTable, 'status_reference_id', $this->integer()->null());

        $this->createIndex(
            'idx-devices-status_reference_id',
            $devicesTable,
            'status_reference_id'
        );

        $this->addForeignKey(
            'fk-devices-status_reference_id',
            $devicesTable,
            'status_reference_id',
            $referenceItemsTable,
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->execute("
            UPDATE {$devicesTable} d
               SET status_reference_id = ri.id
              FROM {$referenceItemsTable} ri
             WHERE ri.legacy_table = 'device_statuses'
               AND ri.legacy_id = d.status_id
        ");

        $notMapped = (new \yii\db\Query())
            ->from($devicesTable)
            ->where(['status_reference_id' => null])
            ->count();

        if ((int)$notMapped > 0) {
            throw new \RuntimeException(
                'Не удалось заполнить status_reference_id для всех записей таблицы devices.'
            );
        }

        $this->alterColumn(
            $devicesTable,
            'status_reference_id',
            $this->integer()->notNull()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $devicesTable = '{{%devices}}';

        $this->dropForeignKey(
            'fk-devices-status_reference_id',
            $devicesTable
        );

        $this->dropIndex(
            'idx-devices-status_reference_id',
            $devicesTable
        );

        $this->dropColumn(
            $devicesTable,
            'status_reference_id'
        );
    }
}
