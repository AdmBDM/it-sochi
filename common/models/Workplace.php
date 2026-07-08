<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int|null $employee_id
 * @property int|null $department_id
 * @property int|null $location_id
 * @property string|null $comment
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string $name
 *
 * Виртуальные атрибуты формы
 * @property int|null $building_id
 * @property string|null $room
 * @property string|null $floor
 *
 * @property Employee|null $employee
 * @property Department|null $department
 * @property Location|null $location
 * @property Device[] $device
 * @property Movement[] $movementsFrom
 * @property Movement[] $movementsTo
 */
class Workplace extends ActiveRecord
{
    /**
     * Виртуальные поля формы.
     */
    public ?int $building_id = null;
    public ?string $room = null;
    public ?string $floor = null;

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%workplaces}}';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['employee_id', 'department_id', 'location_id', 'building_id'], 'integer'],
            [['comment'], 'string'],
            [['room', 'floor'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['employee_id', 'department_id', 'location_id', 'name'], 'required'],
            [['employee_id'], 'exist',
                'targetClass' => Employee::class,
                'targetAttribute' => 'id'
            ],
            [['department_id'], 'exist',
                'targetClass' => Department::class,
                'targetAttribute' => 'id'
            ],
            [['location_id'], 'exist',
                'targetClass' => Location::class,
                'targetAttribute' => 'id'
            ],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'employee_id' => 'Сотрудник',
            'department_id' => 'Подразделение',
            'building_id' => 'Здание',
            'room' => 'Помещение',
            'floor' => 'Этаж',
            'location_id' => 'Расположение',
            'name' => 'Название',
            'comment' => 'Комментарий',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * Заполняет виртуальные поля после загрузки модели.
     */
    public function afterFind(): void
    {
        parent::afterFind();

        if ($this->location !== null) {
            $this->building_id = $this->location->building_id;
            $this->room = $this->location->room;
            $this->floor = $this->location->floor;
        }
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
     * Здание определяется через Location.
     */
    public function getBuilding(): ?Building
    {
        return $this->location?->building;
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
     * Человекочитаемое название рабочего места.
     */
    public function getLabel(): string
    {
        $building = $this->building?->name ?? '';

        return trim(
            ($this->room ?: '-') .
            ' — этаж ' .
            ($this->floor ?: '-') .
            ($building ? " ({$building})" : '')
        );
    }
}