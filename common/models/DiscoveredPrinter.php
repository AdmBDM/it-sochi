<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;

/**
 *
 */
class DiscoveredPrinter extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%discovered_printers}}';
    }

    /**
     * @return ActiveQuery
     */
    public function getDevice(): ActiveQuery
    {
        return $this->hasOne(Device::class, ['id' => 'matched_device_id']);
    }

    /**
     * Автосопоставление по IP
     *
     * @return bool
     * @throws Exception
     */
    public function tryMatchByIp(): bool
    {
        $device = Device::find()
            ->where(['snmp_ip' => $this->ip])
            ->orWhere(['LIKE', 'comment', $this->ip]) // fallback
            ->one();

        if ($device) {
            $this->matched_device_id = $device->id;
            return $this->save(false, ['matched_device_id']);
        }
        return false;
    }
}
