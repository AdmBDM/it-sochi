<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $type
 * @property string $code
 * @property bool $is_active
 * @property string $created_at
 * @property string $updated_at
 *
 * @property CartridgeReplacement[] $replacementsAsShipment
 * @property CartridgeReplacement[] $replacementsAsReturn
 */

class CartridgeTransfer extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'cartridge_transfers';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['type', 'code'], 'required'],
            [['type'], 'in', 'range' => ['shipment', 'return']],
            [['code'], 'string', 'max' => 50],
            [['code'], 'unique'],
            [['is_active'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getReplacementsAsShipment(): ActiveQuery
    {
        return $this->hasMany(CartridgeReplacement::class, ['shipment_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getReplacementsAsReturn(): ActiveQuery
    {
        return $this->hasMany(CartridgeReplacement::class, ['return_id' => 'id']);
    }
}
