<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $building_id
 * @property int|null $floor
 * @property string|null $room
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Building $building
 * @property Workplace[] $workplaces
 */
class Location extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%locations}}';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['building_id'], 'required'],
            [['building_id', 'floor'], 'integer'],
            [['room'], 'string', 'max' => 50],
            [['created_at', 'updated_at'], 'safe'],
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
            'building_id' => 'Здание',
            'floor' => 'Этаж',
            'room' => 'Помещение',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getBuilding(): ActiveQuery
    {
        return $this->hasOne(Building::class, ['id' => 'building_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getWorkplaces(): ActiveQuery
    {
        return $this->hasMany(Workplace::class, ['location_id' => 'id']);
    }
}
