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
        ];
    }

    /**
     * Связь с устройством (device)
     *
     * @return ActiveQuery
     */
    public function getDevice(): ActiveQuery
    {
        return $this->hasOne(Device::class, ['id' => 'device_id']);
    }

    /**
     * Связь с рабочим местом, откуда перемещается техника (старое)
     *
     * @return ActiveQuery
     */
    public function getFromWorkplace(): ActiveQuery
    {
        return $this->hasOne(Workplace::class, ['id' => 'from_workplace_id']);
    }

    /**
     * Связь с рабочим местом, куда перемещается техника (новое)
     *
     * @return ActiveQuery
     */
    public function getToWorkplace(): ActiveQuery
    {
        return $this->hasOne(Workplace::class, ['id' => 'to_workplace_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getMovedByUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'moved_by_user_id']);
    }

    /**
     * Связь с сотрудником, инициировавшим движение
     *
     * @return ActiveQuery
     */
    public function getEmployee(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'moved_by_user_id']);
    }

    /**
     * связь с организацией
     *
     * @return ActiveQuery
     */
    public function getOrganization(): ActiveQuery
    {
        return $this->hasOne(Organization::class, ['id' => 'organization_id']);
    }

    /**
     * связь с отделом
     *
     * @return ActiveQuery
     */
    public function getDepartment(): ActiveQuery
    {
        return $this->hasOne(Department::class, ['id' => 'department_id']);
    }

}
