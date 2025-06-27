<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $full_name
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
            [['full_name', 'email'], 'string', 'max' => 255],
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
}
