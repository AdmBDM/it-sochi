<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $organization_reference_id
 * @property int $building_reference_id
 *
 * @property ReferenceItem $organization
 * @property ReferenceItem $building
 */
class OrganizationBuilding extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%organization_building}}';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['organization_reference_id', 'building_reference_id'], 'required'],
            [['organization_reference_id', 'building_reference_id'], 'integer'],
            [['organization_reference_id', 'building_reference_id'], 'unique',
                'targetAttribute' => ['organization_reference_id', 'building_reference_id']],
            [['organization_reference_id'], 'exist',
                'targetClass' => ReferenceItem::class,
                'targetAttribute' => 'id'],

            [['building_reference_id'], 'exist',
                'targetClass' => ReferenceItem::class,
                'targetAttribute' => 'id'],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'organization_id' => 'Организация',
            'building_id' => 'Здание',
            'organization_reference_id' => 'Организация',
            'building_reference_id' => 'Здание',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getOrganization(): ActiveQuery
    {
        return $this->hasOne(ReferenceItem::class, ['id' => 'organization_reference_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getBuilding(): ActiveQuery
    {
        return $this->hasOne(ReferenceItem::class, ['id' => 'building_reference_id']);
    }
}
