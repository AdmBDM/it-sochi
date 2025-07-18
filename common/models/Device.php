<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "devices".
 *
 * @property int $id
 * @property int $model_id
 * @property int $status_id
 * @property int $workplace_id
 * @property string|null $serial_number
 * @property string|null $inventory_number
 * @property string|null $name
 * @property string|null $comment
 * @property string $created_at
 * @property string $updated_at
 *
 * @property DeviceModel $model
 * @property DeviceStatus $status
 * @property Workplace $workplace
 * @property Movement[] $movements
 */
class Device extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'devices';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['model_id', 'status_id', 'workplace_id'], 'required'],
            [['model_id', 'status_id', 'workplace_id'], 'integer'],
            [['comment'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['serial_number', 'inventory_number', 'name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'model_id' => 'Модель устройства',
            'status_id' => 'Статус',
            'workplace_id' => 'Рабочее место',
            'serial_number' => 'Серийный номер',
            'inventory_number' => 'Инвентарный номер',
            'name' => 'Название',
            'comment' => 'Комментарий',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getModel(): ActiveQuery
    {
        return $this->hasOne(DeviceModel::class, ['id' => 'model_id']);
    }

    public function getStatus(): ActiveQuery
    {
        return $this->hasOne(DeviceStatus::class, ['id' => 'status_id']);
    }

    public function getWorkplace(): ActiveQuery
    {
        return $this->hasOne(Workplace::class, ['id' => 'workplace_id']);
    }

    public function getMovements(): ActiveQuery
    {
        return $this->hasMany(Movement::class, ['device_id' => 'id']);
    }

    // Дополнительные геттеры для вложенных данных

    /**
     * @return mixed|null
     */
    public function getBrand(): mixed
    {
        return $this->model ? $this->model->brand : null;
    }

    /**
     * @return mixed|null
     */
    public function getType(): mixed
    {
        return $this->model ? $this->model->type : null;
    }

    /**
     * @return Employee|null
     */
    public function getEmployee(): ?Employee
    {
        return $this->workplace ? $this->workplace->employee : null;
    }

    /**
     * @return Location|null
     */
    public function getLocation(): ?Location
    {
        return $this->workplace ? $this->workplace->location : null;
    }
}
