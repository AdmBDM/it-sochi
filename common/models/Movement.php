<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $device_id
 * @property int|null $from_workplace_id
 * @property int|null $to_workplace_id
 * @property int|null $moved_by_user_id
 * @property string|null $note
 * @property string $moved_at
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Device $device
 * @property Workplace|null $fromWorkplace
 * @property Workplace|null $toWorkplace
 * @property User|null $movedByUser
 */
class Movement extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%movements}}';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['device_id', 'moved_at'], 'required'],
            [['device_id', 'from_workplace_id', 'to_workplace_id', 'moved_by_user_id'], 'integer'],
            [['moved_at', 'created_at', 'updated_at'], 'safe'],
            [['note'], 'string'],
            [['device_id'], 'exist', 'targetClass' => Device::class, 'targetAttribute' => 'id'],
            [['from_workplace_id'], 'exist', 'targetClass' => Workplace::class, 'targetAttribute' => 'id'],
            [['to_workplace_id'], 'exist', 'targetClass' => Workplace::class, 'targetAttribute' => 'id'],
            [['moved_by_user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'device_id' => 'Устройство',
            'from_workplace_id' => 'С места',
            'to_workplace_id' => 'На место',
            'moved_by_user_id' => 'Переместил',
            'moved_at' => 'Дата перемещения',
            'note' => 'Примечание',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
            'type_change' => 'Тип',
        ];
    }

    // -------------------------
    // Связи с другими таблицами
    // -------------------------
    public function getDevice()
    {
        return $this->hasOne(Device::class, ['id' => 'device_id']);
    }

    public function getOldWorkplace()
    {
//        return $this->hasOne(Workplace::class, ['id' => 'id_old'])->andWhere(['type_change' => 'place']);
        return $this->hasOne(Workplace::class, ['id' => 'id_old']);
    }

    public function getNewWorkplace()
    {
//        return $this->hasOne(Workplace::class, ['id' => 'id_new'])->andWhere(['type_change' => 'place']);
        return $this->hasOne(Workplace::class, ['id' => 'id_new']);
    }

    public function getOldStatus()
    {
//        return $this->hasOne(DeviceStatus::class, ['id' => 'id_old'])->andWhere(['type_change' => 'status']);
        return $this->hasOne(DeviceStatus::class, ['id' => 'id_old']);
    }

    public function getNewStatus()
    {
//        return $this->hasOne(DeviceStatus::class, ['id' => 'id_new'])->andWhere(['type_change' => 'status']);
        return $this->hasOne(DeviceStatus::class, ['id' => 'id_new']);
    }

    public function getEmployee()
    {
        return $this->hasOne(Employee::class, ['id' => 'moved_by_user_id']);
    }

    // -------------------------
    // Виртуальные свойства
    // -------------------------
    public function getOldValue()
    {
        if ($this->type_change === 'place') {
            return $this->oldWorkplace ? $this->oldWorkplace->name : null;
        }
        return $this->oldStatus ? $this->oldStatus->name : null;
    }

    public function getNewValue()
    {
        if ($this->type_change === 'place') {
            return $this->newWorkplace ? $this->newWorkplace->name : null;
        }
        return $this->newStatus ? $this->newStatus->name : null;
    }
}
