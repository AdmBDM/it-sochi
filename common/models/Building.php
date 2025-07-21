<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 * @property string $small
 * @property string|null $address
 * @property string $created_at
 * @property string $updated_at
 *
 * @property OrganizationBuilding[] $organizationBuildings
 * @property Location[] $locations
 */
class Building extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%buildings}}';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['address'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'small'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Название здания',
            'small' => 'Кратко',
            'address' => 'Адрес',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getOrganizationBuildings(): ActiveQuery
    {
        return $this->hasMany(OrganizationBuilding::class, ['building_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLocations(): ActiveQuery
    {
        return $this->hasMany(Location::class, ['building_id' => 'id']);
    }
}
