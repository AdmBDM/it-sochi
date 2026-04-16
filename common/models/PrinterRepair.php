<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

class PrinterRepair extends ActiveRecord
{
    public $partsInput = []; // для формы

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%printer_repairs}}';
    }

    /**
     * @return array[]
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['device_id', 'service_center', 'description', 'started_at'], 'required'],
            [['device_id'], 'integer'],
            [['cost'], 'number', 'min' => 0],
            [['description'], 'string'],
            [['started_at', 'finished_at'], 'date', 'format' => 'php:Y-m-d'],
            [['finished_at'], 'validateFinishedAt'],
            [['service_center', 'document_number'], 'string', 'max' => 255],
            [['partsInput'], 'safe'],
        ];
    }

    /**
     * @param $attribute
     *
     * @return void
     */
    public function validateFinishedAt($attribute): void
    {
        if ($this->finished_at && $this->started_at) {
            if (strtotime($this->finished_at) < strtotime($this->started_at)) {
                $this->addError($attribute, 'Дата окончания не может быть раньше даты начала');
            }
        }
    }

    /**
     * @return void
     */
    public function afterFind(): void
    {
        parent::afterFind();
        $this->partsInput = $this->parts_replaced ? json_decode($this->parts_replaced, true) : [];
    }

    /**
     * @param $insert
     *
     * @return bool
     */
    public function beforeSave($insert): bool
    {
        if (!parent::beforeSave($insert)) return false;

        $this->parts_replaced = !empty($this->partsInput)
            ? json_encode(array_values(array_filter($this->partsInput)))
            : null;

        return true;
    }

    /**
     * @return ActiveQuery
     */
    public function getDevice(): ActiveQuery
    {
        return $this->hasOne(Device::class, ['id' => 'device_id']);
    }

    /**
     * @return int|null
     * @throws \DateMalformedStringException
     */
    public function getDurationDays(): ?int
    {
        if (!$this->finished_at) return null;
        $start = new \DateTime($this->started_at);
        $end = new \DateTime($this->finished_at);
        return $start->diff($end)->days + 1;
    }

    /**
     * Для GridView: статус ремонта
     *
     * @return string
     */
    public function getStatusLabel(): string
    {
        if (!$this->finished_at) return '<span class="badge bg-warning">В ремонте</span>';
        return '<span class="badge bg-success">Завершён</span>';
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'device_id' => 'Принтер',
            'service_center' => 'Сервисный центр',
            'description' => 'Описание работ',
            'partsInput' => 'Заменённые запчасти',
            'cost' => 'Стоимость',
            'started_at' => 'Дата начала',
            'finished_at' => 'Дата окончания',
            'document_number' => 'Номер документа',
        ];
    }
}
