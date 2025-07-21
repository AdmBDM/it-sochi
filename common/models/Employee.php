<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $full_name
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property string|null $email
 * @property string|null $phone
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Workplace[] $workplaces
 */
class Employee extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%employees}}';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['full_name'], 'required'],
            [['full_name', 'first_name', 'middle_name', 'last_name', 'email'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 50],
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
            'full_name' => 'ФИО',
            'first_name' => 'Имя',
            'middle_name' => 'Отчество',
            'last_name' => 'Фамилия',
            'email' => 'Email',
            'phone' => 'Телефон',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getWorkplaces(): ActiveQuery
    {
        return $this->hasMany(Workplace::class, ['employee_id' => 'id']);
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
//        return $this->lastname . ' ' . $this->firstname;
        return $this->full_name;
    }

}
