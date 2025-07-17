<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "worker_schedule".
 *
 * @property int $id
 * @property string $source_id  ← Добавлено!
 * @property string $worker_name             ФИО сотрудника
 * @property string|null $avatar             Имя аватара (если есть)
 * @property string|null $car_number         Госномер автомобиля
 * @property string|null $car_region         Регион госномера
 * @property string|null $car_model          Модель автомобиля
 * @property string|null $start_repair       Время начала ремонта
 * @property string|null $finish_repair      Время окончания ремонта
 * @property string|null $condition          Статус или этап ремонта
 * @property string $created_at              Дата создания записи
 * @property string $updated_at              Дата изменения записи
 */
class WorkerSchedule extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%worker_schedule}}';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['worker_name'], 'required'],
            [['worker_name', 'avatar', 'car_number', 'car_model', 'condition', 'source_id'], 'string', 'max' => 255],
            [['car_region'], 'string', 'max' => 10],
            [['start_repair', 'finish_repair'], 'string', 'max' => 10],
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
            'worker_name' => 'ФИО мастера',
            'avatar' => 'Аватар',
            'car_number' => 'Номер',
            'car_region' => 'Регион',
            'car_model' => 'Модель',
            'start_repair' => 'Начало',
            'finish_repair' => 'Окончание',
            'condition' => 'Статус',
            'created_at' => 'Создано',
            'updated_at' => 'Изменено',
        ];
    }
}
