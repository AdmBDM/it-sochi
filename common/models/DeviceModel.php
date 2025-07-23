<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $brand_id
 * @property int $type_id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 *
 * @property DeviceBrand $deviceBrand
 * @property DeviceType $deviceType
 * @property Device[] $devices
 */
class DeviceModel extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%device_models}}';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['brand_id', 'type_id', 'name'], 'required'],
            [['brand_id', 'type_id'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['created_at', 'updated_at'], 'safe'],
            [['brand_id'], 'exist', 'targetClass' => DeviceBrand::class, 'targetAttribute' => 'id'],
            [['type_id'], 'exist', 'targetClass' => DeviceType::class, 'targetAttribute' => 'id'],
            [['type_id', 'name'], 'unique', 'targetAttribute' => ['type_id', 'name'], 'message' => 'Такое сочетание типа и названия уже существует.'],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'brand_id' => 'Бренд',
            'type_id' => 'Тип устройства',
            'name' => 'Модель',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getBrand(): ActiveQuery
    {
        return $this->hasOne(DeviceBrand::class, ['id' => 'brand_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getType(): ActiveQuery
    {
        return $this->hasOne(DeviceType::class, ['id' => 'type_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDevices(): ActiveQuery
    {
        return $this->hasMany(Device::class, ['model_id' => 'id']);
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        $brand = $this->brand->name ?? '';
        return "{$this->name} ({$brand})";
    }


}
