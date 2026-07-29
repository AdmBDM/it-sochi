<?php

use yii\db\Migration;

class m260729_133806_make_legacy_reference_fields_nullable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $deviceModelsTable = '{{%device_models}}';
        $devicesTable = '{{%devices}}';
        $locationsTable = '{{%locations}}';
        $organizationBuildingTable = '{{%organization_building}}';

        $this->alterColumn(
            $deviceModelsTable,
            'brand_id',
            $this->integer()->null()
        );

        $this->alterColumn(
            $deviceModelsTable,
            'type_id',
            $this->integer()->null()
        );

        $this->alterColumn(
            $devicesTable,
            'status_id',
            $this->integer()->null()
        );

        $this->alterColumn(
            $locationsTable,
            'building_id',
            $this->integer()->null()
        );

        $this->alterColumn(
            $organizationBuildingTable,
            'organization_id',
            $this->integer()->null()
        );

        $this->alterColumn(
            $organizationBuildingTable,
            'building_id',
            $this->integer()->null()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $deviceModelsTable = '{{%device_models}}';
        $devicesTable = '{{%devices}}';
        $locationsTable = '{{%locations}}';
        $organizationBuildingTable = '{{%organization_building}}';

        $this->alterColumn(
            $deviceModelsTable,
            'brand_id',
            $this->integer()->notNull()
        );

        $this->alterColumn(
            $deviceModelsTable,
            'type_id',
            $this->integer()->notNull()
        );

        $this->alterColumn(
            $devicesTable,
            'status_id',
            $this->integer()->notNull()
        );

        $this->alterColumn(
            $locationsTable,
            'building_id',
            $this->integer()->notNull()
        );

        $this->alterColumn(
            $organizationBuildingTable,
            'organization_id',
            $this->integer()->notNull()
        );

        $this->alterColumn(
            $organizationBuildingTable,
            'building_id',
            $this->integer()->notNull()
        );
    }
}
