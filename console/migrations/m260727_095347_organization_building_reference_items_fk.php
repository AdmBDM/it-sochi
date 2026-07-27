<?php

use yii\db\Migration;

class m260727_095347_organization_building_reference_items_fk extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $organizationBuildingTable = '{{%organization_building}}';
        $referenceItemsTable = '{{%reference_items}}';

        $this->addColumn($organizationBuildingTable, 'organization_reference_id', $this->integer()->null());
        $this->addColumn($organizationBuildingTable, 'building_reference_id', $this->integer()->null());

        $this->createIndex(
            'idx-organization_building-organization_reference_id',
            $organizationBuildingTable,
            'organization_reference_id'
        );

        $this->createIndex(
            'idx-organization_building-building_reference_id',
            $organizationBuildingTable,
            'building_reference_id'
        );

        $this->addForeignKey(
            'fk-organization_building-organization_reference_id',
            $organizationBuildingTable,
            'organization_reference_id',
            $referenceItemsTable,
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-organization_building-building_reference_id',
            $organizationBuildingTable,
            'building_reference_id',
            $referenceItemsTable,
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->execute("
            UPDATE {$organizationBuildingTable} ob
               SET organization_reference_id = ri.id
              FROM {$referenceItemsTable} ri
             WHERE ri.legacy_table = 'organizations'
               AND ri.legacy_id = ob.organization_id
        ");

        $this->execute("
            UPDATE {$organizationBuildingTable} ob
               SET building_reference_id = ri.id
              FROM {$referenceItemsTable} ri
             WHERE ri.legacy_table = 'buildings'
               AND ri.legacy_id = ob.building_id
        ");

        $organizationNotMapped = (new \yii\db\Query())
            ->from($organizationBuildingTable)
            ->where(['organization_reference_id' => null])
            ->count();

        if ((int)$organizationNotMapped > 0) {
            throw new \RuntimeException(
                'Не удалось заполнить organization_reference_id для всех записей таблицы organization_building.'
            );
        }

        $buildingNotMapped = (new \yii\db\Query())
            ->from($organizationBuildingTable)
            ->where(['building_reference_id' => null])
            ->count();

        if ((int)$buildingNotMapped > 0) {
            throw new \RuntimeException(
                'Не удалось заполнить building_reference_id для всех записей таблицы organization_building.'
            );
        }

        $this->alterColumn(
            $organizationBuildingTable,
            'organization_reference_id',
            $this->integer()->notNull()
        );

        $this->alterColumn(
            $organizationBuildingTable,
            'building_reference_id',
            $this->integer()->notNull()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $organizationBuildingTable = '{{%organization_building}}';

        $this->dropForeignKey(
            'fk-organization_building-organization_reference_id',
            $organizationBuildingTable
        );

        $this->dropForeignKey(
            'fk-organization_building-building_reference_id',
            $organizationBuildingTable
        );

        $this->dropIndex(
            'idx-organization_building-organization_reference_id',
            $organizationBuildingTable
        );

        $this->dropIndex(
            'idx-organization_building-building_reference_id',
            $organizationBuildingTable
        );

        $this->dropColumn(
            $organizationBuildingTable,
            'organization_reference_id'
        );

        $this->dropColumn(
            $organizationBuildingTable,
            'building_reference_id'
        );
    }
}
