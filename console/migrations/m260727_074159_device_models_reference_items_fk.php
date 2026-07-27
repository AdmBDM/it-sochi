<?php

use yii\db\Migration;

class m260727_074159_device_models_reference_items_fk extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $deviceModelsTable = '{{%device_models}}';
        $referenceItemsTable = '{{%reference_items}}';

        $this->addColumn($deviceModelsTable, 'brand_reference_id', $this->integer()->null());
        $this->addColumn($deviceModelsTable, 'type_reference_id', $this->integer()->null());

        $this->createIndex(
            'idx-device_models-brand_reference_id',
            $deviceModelsTable,
            'brand_reference_id'
        );

        $this->createIndex(
            'idx-device_models-type_reference_id',
            $deviceModelsTable,
            'type_reference_id'
        );

        $this->addForeignKey(
            'fk-device_models-brand_reference_id',
            $deviceModelsTable,
            'brand_reference_id',
            $referenceItemsTable,
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-device_models-type_reference_id',
            $deviceModelsTable,
            'type_reference_id',
            $referenceItemsTable,
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->execute("
            UPDATE {$deviceModelsTable} dm
            SET brand_reference_id = ri.id
            FROM {$referenceItemsTable} ri
            WHERE ri.legacy_table = 'device_brands'
              AND ri.legacy_id = dm.brand_id
        ");

        $this->execute("
            UPDATE {$deviceModelsTable} dm
            SET type_reference_id = ri.id
            FROM {$referenceItemsTable} ri
            WHERE ri.legacy_table = 'device_types'
              AND ri.legacy_id = dm.type_id
        ");

        $brandNotMapped = (new \yii\db\Query())
            ->from($deviceModelsTable)
            ->where(['brand_reference_id' => null])
            ->count();

        if ((int)$brandNotMapped > 0) {
            throw new \RuntimeException('Не удалось заполнить brand_reference_id для всех записей device_models.');
        }

        $typeNotMapped = (new \yii\db\Query())
            ->from($deviceModelsTable)
            ->where(['type_reference_id' => null])
            ->count();

        if ((int)$typeNotMapped > 0) {
            throw new \RuntimeException('Не удалось заполнить type_reference_id для всех записей device_models.');
        }

        $this->alterColumn(
            $deviceModelsTable,
            'brand_reference_id',
            $this->integer()->notNull()
        );

        $this->alterColumn(
            $deviceModelsTable,
            'type_reference_id',
            $this->integer()->notNull()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $deviceModelsTable = '{{%device_models}}';

        $this->dropForeignKey(
            'fk-device_models-brand_reference_id',
            $deviceModelsTable
        );

        $this->dropForeignKey(
            'fk-device_models-type_reference_id',
            $deviceModelsTable
        );

        $this->dropIndex(
            'idx-device_models-brand_reference_id',
            $deviceModelsTable
        );

        $this->dropIndex(
            'idx-device_models-type_reference_id',
            $deviceModelsTable
        );

        $this->dropColumn($deviceModelsTable, 'brand_reference_id');
        $this->dropColumn($deviceModelsTable, 'type_reference_id');
    }
}
