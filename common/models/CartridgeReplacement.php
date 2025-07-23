<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $device_id
 * @property int|null $old_cartridge_type_id
 * @property int $new_cartridge_type_id
 * @property int|null $shipment_id
 * @property int|null $return_id
 * @property string $replaced_at
 * @property bool $is_active
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Device $device
 * @property CartridgeType $oldCartridgeType
 * @property CartridgeType $newCartridgeType
 * @property CartridgeTransfer $shipment
 * @property CartridgeTransfer $return
 */

class CartridgeReplacement extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'cartridge_replacements';
    }

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['device_id', 'new_cartridge_type_id'], 'required'],
            [['device_id', 'old_cartridge_type_id', 'new_cartridge_type_id', 'shipment_id', 'return_id'], 'integer'],
            [['replaced_at', 'created_at', 'updated_at'], 'safe'],
            [['is_active'], 'boolean'],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getDevice(): ActiveQuery
    {
        return $this->hasOne(Device::class, ['id' => 'device_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOldCartridgeType(): ActiveQuery
    {
        return $this->hasOne(CartridgeType::class, ['id' => 'old_cartridge_type_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getNewCartridgeType(): ActiveQuery
    {
        return $this->hasOne(CartridgeType::class, ['id' => 'new_cartridge_type_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getShipment(): ActiveQuery
    {
        return $this->hasOne(CartridgeTransfer::class, ['id' => 'shipment_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getReturn(): ActiveQuery
    {
        return $this->hasOne(CartridgeTransfer::class, ['id' => 'return_id']);
    }
}
