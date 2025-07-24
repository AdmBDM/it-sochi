<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 * @property string|null $color
 * @property int $initial_quantity
 * @property bool $is_active
 * @property string $created_at
 * @property string $updated_at
 *
 * @property DeviceCartridgeType[] $deviceLinks
 * @property CartridgeReplacement[] $replacementsAsOld
 * @property CartridgeReplacement[] $replacementsAsNew
 */

class CartridgeType extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'cartridge_types';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['name'], 'required'],
//            [['name'], 'unique'],
            [['initial_quantity'], 'integer'],
            [['color'], 'string', 'max' => 50],
            [['name'], 'string', 'max' => 255],
            [['is_active'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'color'], 'unique', 'targetAttribute' => ['name', 'color'], 'message' => 'Такой картридж уже существует.'],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Модель',
            'color' => 'Цвет',
            'initial_quantity' => 'Начальное кол-во',
            'is_active' => 'В работе',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getDeviceLinks(): ActiveQuery
    {
        return $this->hasMany(DeviceCartridgeType::class, ['cartridge_type_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getReplacementsAsOld(): ActiveQuery
    {
        return $this->hasMany(CartridgeReplacement::class, ['old_cartridge_type_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getReplacementsAsNew(): ActiveQuery
    {
        return $this->hasMany(CartridgeReplacement::class, ['new_cartridge_type_id' => 'id']);
    }
}
