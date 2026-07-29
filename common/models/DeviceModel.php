<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $brand_reference_id
 * @property int $type_reference_id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ReferenceItem $brand
 * @property ReferenceItem $type
 *
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
            [['brand_reference_id', 'type_reference_id', 'name'], 'required'],
            [['brand_reference_id', 'type_reference_id'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['created_at', 'updated_at'], 'safe'],
            [['brand_reference_id'], 'exist', 'targetClass' => ReferenceItem::class, 'targetAttribute' => 'id'],
            [['type_reference_id'], 'exist', 'targetClass' => ReferenceItem::class, 'targetAttribute' => 'id'],
            [['type_reference_id', 'name'], 'unique', 'targetAttribute' => ['type_reference_id', 'name'], 'message' => 'Такое сочетание типа и названия уже существует.'],
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
            'brand_reference_id' => 'Бренд',
            'type_reference_id' => 'Тип устройства',
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
        return $this->hasOne(ReferenceItem::class, ['id' => 'brand_reference_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getType(): ActiveQuery
    {
        return $this->hasOne(ReferenceItem::class, ['id' => 'type_reference_id']);
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
