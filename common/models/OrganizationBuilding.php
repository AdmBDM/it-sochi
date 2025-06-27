<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $organization_id
 * @property int $building_id
 *
 * @property Organization $organization
 * @property Building $building
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
            [['organization_id', 'building_id'], 'required'],
            [['organization_id', 'building_id'], 'integer'],
            [['organization_id', 'building_id'], 'unique', 'targetAttribute' => ['organization_id', 'building_id']],
            [['organization_id'], 'exist', 'targetClass' => Organization::class, 'targetAttribute' => 'id'],
            [['building_id'], 'exist', 'targetClass' => Building::class, 'targetAttribute' => 'id'],
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
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getOrganization(): ActiveQuery
    {
        return $this->hasOne(Organization::class, ['id' => 'organization_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getBuilding(): ActiveQuery
    {
        return $this->hasOne(Building::class, ['id' => 'building_id']);
    }
}
