<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $device_id
 * @property int $cartridge_type_id
 * @property bool $is_active
 * @property string $created_at
 * @property string $updated_at
 *
 * @property CartridgeType $cartridgeType
 * @property Device $device
 */

class DeviceCartridgeType extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'device_cartridge_type';
    }

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['device_id', 'cartridge_type_id'], 'required'],
            [['device_id', 'cartridge_type_id'], 'integer'],
            [['is_active'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCartridgeType(): ActiveQuery
    {
        return $this->hasOne(CartridgeType::class, ['id' => 'cartridge_type_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDevice(): ActiveQuery
    {
        return $this->hasOne(Device::class, ['id' => 'device_id']);
    }
}
