<?php

use yii\db\Migration;

class m260727_095827_buildings_reference_items_fk extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $buildingsTable = '{{%buildings}}';
        $referenceItemsTable = '{{%reference_items}}';

        $this->addColumn($buildingsTable, 'type_reference_id', $this->integer()->null());

        $this->createIndex(
            'idx-buildings-type_reference_id',
            $buildingsTable,
            'type_reference_id'
        );

        $this->addForeignKey(
            'fk-buildings-type_reference_id',
            $buildingsTable,
            'type_reference_id',
            $referenceItemsTable,
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->execute("
            UPDATE {$buildingsTable} b
               SET type_reference_id = ri.id
              FROM {$referenceItemsTable} ri
             WHERE ri.legacy_table = 'device_types'
               AND ri.legacy_id = b.id
        ");

        $notMapped = (new \yii\db\Query())
            ->from($buildingsTable)
            ->where(['type_reference_id' => null])
            ->count();

        if ((int)$notMapped > 0) {
            throw new \RuntimeException(
                'Не удалось заполнить type_reference_id для всех записей таблицы buildings.'
            );
        }

        $this->alterColumn(
            $buildingsTable,
            'type_reference_id',
            $this->integer()->notNull()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $buildingsTable = '{{%buildings}}';

        $this->dropForeignKey(
            'fk-buildings-type_reference_id',
            $buildingsTable
        );

        $this->dropIndex(
            'idx-buildings-type_reference_id',
            $buildingsTable
        );

        $this->dropColumn(
            $buildingsTable,
            'type_reference_id'
        );
    }
}
