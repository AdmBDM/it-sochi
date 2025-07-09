<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $device_model_id
 * @property int|null $workplace_id
 * @property int|null $device_status_id
 * @property string|null $serial
 * @property string|null $inventory
 * @property string|null $note
 * @property string $created_at
 * @property string $updated_at
 * @property string $name
 *
 * @property DeviceModel $deviceModel
 * @property DeviceStatus|null $deviceStatus
 * @property Workplace|null $workplace
 * @property Movement[] $movements
 */
class Device extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%devices}}';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['device_model_id'], 'required'],
            [['device_model_id', 'workplace_id', 'device_status_id'], 'integer'],
            [['serial', 'inventory'], 'string', 'max' => 100],
            [['note'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['serial'], 'unique'],
            [['inventory'], 'unique'],
            [['device_model_id'], 'exist', 'targetClass' => DeviceModel::class, 'targetAttribute' => 'id'],
            [['workplace_id'], 'exist', 'targetClass' => Workplace::class, 'targetAttribute' => 'id'],
            [['device_status_id'], 'exist', 'targetClass' => DeviceStatus::class, 'targetAttribute' => 'id'],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'device_model_id' => 'Модель устройства',
            'workplace_id' => 'Рабочее место',
            'device_status_id' => 'Статус',
            'serial' => 'Серийный номер',
            'inventory' => 'Инвентарный номер',
            'note' => 'Примечание',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getDeviceModel(): ActiveQuery
    {
        return $this->hasOne(DeviceModel::class, ['id' => 'device_model_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDeviceStatus(): ActiveQuery
    {
        return $this->hasOne(DeviceStatus::class, ['id' => 'device_status_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getWorkplace(): ActiveQuery
    {
        return $this->hasOne(Workplace::class, ['id' => 'workplace_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getMovements(): ActiveQuery
    {
        return $this->hasMany(Movement::class, ['device_id' => 'id']);
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        $type = $this->type->name ?? '—';
        $brand = $this->brand->name ?? '';
        $model = $this->model->name ?? '';
        $serial = $this->serial_number ?? '';
//        return $this->name ?? "{$type} {$brand} {$model} / SN: {$serial}";
        return $this->name ?? "{$type} {$brand} {$model}";
    }
}
