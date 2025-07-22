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
    public string $fam_io;

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
//            [['fam_io'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 50],
            [['created_at', 'updated_at', 'fam_io'], 'safe'],
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
            'fam_io' => 'Фамилия И.О.',
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
        return $this->last_name . ' ' . $this->first_name . ' ' . $this->middle_name;
//        return $this->full_name;
    }

    /**
     * @return string
     */
    public function getFamIO(): string
    {
        return ($this->last_name . ' '
            . trim(' ' . ($this->first_name ? mb_substr($this->first_name, 0, 1) . '.' : ''))
            . trim(' ' . ($this->middle_name ? mb_substr($this->middle_name, 0, 1) . '.' : '')));
    }

}
