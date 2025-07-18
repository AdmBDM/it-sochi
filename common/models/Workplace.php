<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int|null $employee_id
 * @property int|null $department_id
 * @property int|null $location_id
 * @property string|null $note
 * @property string $created_at
 * @property string $updated_at
 * @property string $name
 *
 * @property Employee|null $employee
 * @property Department|null $department
 * @property Location|null $location
 * @property Device[] $devices
 * @property Movement[] $movementsFrom
 * @property Movement[] $movementsTo
 */

/**
 * Workplace — модель для рабочего места.
 */
class Workplace extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%workplaces}}';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['employee_id', 'department_id', 'location_id'], 'integer'],
            [['comment'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['employee_id', 'location_id', 'department_id', 'name'], 'required'],
            [['employee_id'], 'exist', 'targetClass' => Employee::class, 'targetAttribute' => 'id'],
            [['department_id'], 'exist', 'targetClass' => Department::class, 'targetAttribute' => 'id'],
            [['location_id'], 'exist', 'targetClass' => Location::class, 'targetAttribute' => 'id'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'employee_id' => 'Сотрудник',
            'department_id' => 'Отдел',
            'location_id' => 'Расположение',
            'name' => 'Название',
            'comment' => 'Комментарий',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getEmployee(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'employee_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDepartment(): ActiveQuery
    {
        return $this->hasOne(Department::class, ['id' => 'department_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLocation(): ActiveQuery
    {
        return $this->hasOne(Location::class, ['id' => 'location_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDevice(): ActiveQuery
    {
        return $this->hasMany(Device::class, ['workplace_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getMovementsFrom(): ActiveQuery
    {
        return $this->hasMany(Movement::class, ['from_workplace_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getMovementsTo(): ActiveQuery
    {
        return $this->hasMany(Movement::class, ['to_workplace_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getBuilding(): ActiveQuery
    {
        return $this->hasOne(Building::class, ['id' => 'building_id']);
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        $building = $this->building->name ?? '';
        return "{$this->room} — {$this->floor} этаж, {$building}";
    }

}
