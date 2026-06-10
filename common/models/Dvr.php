<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 * @property string $system_type
 * @property string $ip_address
 * @property int $port
 * @property string $network
 * @property string|null $username
 * @property string|null $password
 * @property string|null $sdk_password
 * @property int $status
 * @property string|null $last_scan_at
 * @property string|null $model
 * @property string|null $firmware
 * @property string|null $serial_number
 * @property string|null $mac_address
 * @property int|null $channel_count
 * @property array|null $disk_info
 * @property array|null $extra_data
 * @property string $created_at
 * @property string $updated_at
 */
class Dvr extends ActiveRecord
{
    public const string SYSTEM_TRASSIR = 'trassir';
    public const string SYSTEM_DAHUA = 'dahua';
    public const string SYSTEM_HIKVISION = 'hikvision';

    public const int STATUS_ACTIVE = 1;
    public const int STATUS_INACTIVE = 0;

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%dvr}}';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['name', 'system_type', 'ip_address', 'port', 'network'], 'required'],
            [['system_type'], 'in', 'range' => [self::SYSTEM_TRASSIR, self::SYSTEM_DAHUA, self::SYSTEM_HIKVISION]],
            [['port', 'status', 'channel_count'], 'integer'],
            [['disk_info', 'extra_data'], 'safe'],
            [['ip_address'], 'ip'],
            [['mac_address'], 'match', 'pattern' => '/^([0-9A-Fa-f]{2}:){5}[0-9A-Fa-f]{2}$/'],
            [['last_scan_at', 'created_at', 'updated_at'], 'safe'],
            [['name', 'model', 'firmware', 'serial_number'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCameras(): ActiveQuery
    {
        return $this->hasMany(Camera::class, ['dvr_id' => 'id']);
    }

    /**
     * @return string
     */
    public function getSystemLabel(): string
    {
        return match ($this->system_type) {
            self::SYSTEM_TRASSIR => 'Trassir',
            self::SYSTEM_DAHUA => 'Dahua',
            self::SYSTEM_HIKVISION => 'Hikvision',
            default => $this->system_type,
        };
    }
}
