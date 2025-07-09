<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $device_brand_id
 * @property int $device_type_id
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
            [['device_brand_id', 'device_type_id', 'name'], 'required'],
            [['device_brand_id', 'device_type_id'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['name'], 'unique'],
            [['created_at', 'updated_at'], 'safe'],
            [['device_brand_id'], 'exist', 'targetClass' => DeviceBrand::class, 'targetAttribute' => 'id'],
            [['device_type_id'], 'exist', 'targetClass' => DeviceType::class, 'targetAttribute' => 'id'],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'device_brand_id' => 'Бренд',
            'device_type_id' => 'Тип устройства',
            'name' => 'Модель',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getDeviceBrand(): ActiveQuery
    {
        return $this->hasOne(DeviceBrand::class, ['id' => 'device_brand_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDeviceType(): ActiveQuery
    {
        return $this->hasOne(DeviceType::class, ['id' => 'device_type_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDevices(): ActiveQuery
    {
        return $this->hasMany(Device::class, ['device_model_id' => 'id']);
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
