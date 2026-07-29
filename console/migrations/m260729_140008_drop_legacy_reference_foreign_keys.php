<?php

use yii\db\Migration;

class m260729_140008_drop_legacy_reference_foreign_keys extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $deviceBrandsTable = '{{%device_brands}}';
        $deviceModelsTable = '{{%device_models}}';
        $deviceStatusesTable = '{{%device_statuses}}';
        $deviceTypesTable = '{{%device_types}}';
        $devicesTable = '{{%devices}}';
        $buildingsTable = '{{%buildings}}';
        $locationsTable = '{{%locations}}';

        unset(
            $deviceBrandsTable,
            $deviceStatusesTable,
            $deviceTypesTable,
            $buildingsTable
        );

        $this->dropForeignKey(
            'fk_device_models_brand',
            $deviceModelsTable
        );

        $this->dropForeignKey(
            'fk_device_models_type_id',
            $deviceModelsTable
        );

        $this->dropForeignKey(
            'fk_devices_status',
            $devicesTable
        );

        $this->dropForeignKey(
            'fk_locations_building',
            $locationsTable
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $deviceBrandsTable = '{{%device_brands}}';
        $deviceModelsTable = '{{%device_models}}';
        $deviceStatusesTable = '{{%device_statuses}}';
        $deviceTypesTable = '{{%device_types}}';
        $devicesTable = '{{%devices}}';
        $buildingsTable = '{{%buildings}}';
        $locationsTable = '{{%locations}}';

        $this->addForeignKey(
            'fk_device_models_brand',
            $deviceModelsTable,
            'brand_id',
            $deviceBrandsTable,
            'id',
            'CASCADE',
            'NO ACTION'
        );

        $this->addForeignKey(
            'fk_device_models_type_id',
            $deviceModelsTable,
            'type_id',
            $deviceTypesTable,
            'id',
            'RESTRICT',
            'NO ACTION'
        );

        $this->addForeignKey(
            'fk_devices_status',
            $devicesTable,
            'status_id',
            $deviceStatusesTable,
            'id',
            'RESTRICT',
            'NO ACTION'
        );

        $this->addForeignKey(
            'fk_locations_building',
            $locationsTable,
            'building_id',
            $buildingsTable,
            'id',
            'CASCADE',
            'NO ACTION'
        );
    }
}
