<?php

use yii\db\Migration;

class m260727_083210_locations_reference_items_fk extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $locationsTable = '{{%locations}}';
        $referenceItemsTable = '{{%reference_items}}';

        $this->addColumn($locationsTable, 'building_reference_id', $this->integer()->null());

        $this->createIndex(
            'idx-locations-building_reference_id',
            $locationsTable,
            'building_reference_id'
        );

        $this->addForeignKey(
            'fk-locations-building_reference_id',
            $locationsTable,
            'building_reference_id',
            $referenceItemsTable,
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->execute("
            UPDATE {$locationsTable} l
               SET building_reference_id = ri.id
              FROM {$referenceItemsTable} ri
             WHERE ri.legacy_table = 'buildings'
               AND ri.legacy_id = l.building_id
        ");

        $notMapped = (new \yii\db\Query())
            ->from($locationsTable)
            ->where(['building_reference_id' => null])
            ->count();

        if ((int)$notMapped > 0) {
            throw new \RuntimeException(
                'Не удалось заполнить building_reference_id для всех записей таблицы locations.'
            );
        }

        $this->alterColumn(
            $locationsTable,
            'building_reference_id',
            $this->integer()->notNull()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $locationsTable = '{{%locations}}';

        $this->dropForeignKey(
            'fk-locations-building_reference_id',
            $locationsTable
        );

        $this->dropIndex(
            'idx-locations-building_reference_id',
            $locationsTable
        );

        $this->dropColumn(
            $locationsTable,
            'building_reference_id'
        );
    }
}
