<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $dvr_id
 * @property string|null $name
 * @property int|null $channel_no
 * @property string|null $guid
 * @property string|null $ip_address
 * @property string|null $mac_address
 * @property string|null $model
 * @property string|null $firmware
 * @property string|null $serial_number
 * @property int $status
 * @property string|null $stream_main
 * @property string|null $stream_sub
 * @property string|null $resolution
 * @property int|null $fps
 * @property string|null $codec
 * @property bool $ptz_supported
 * @property string|null $ptz_protocol
 * @property string|null $location
 * @property array|null $extra_data
 * @property string $created_at
 * @property string $updated_at
 */
class Camera extends ActiveRecord
{
    public const int STATUS_ONLINE = 1;
    public const int STATUS_OFFLINE = 0;

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%camera}}';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['dvr_id'], 'required'],
            [['dvr_id'], 'exist', 'targetClass' => Dvr::class, 'targetAttribute' => 'id'],
            [['channel_no', 'status', 'fps'], 'integer'],
            [['ptz_supported'], 'boolean'],
            [['extra_data'], 'safe'],
            [['ip_address'], 'ip'],
            [['mac_address'], 'match', 'pattern' => '/^([0-9A-Fa-f]{2}:){5}[0-9A-Fa-f]{2}$/', 'skipOnEmpty' => true],
            [['stream_main', 'stream_sub', 'location'], 'string'],
            [['name', 'model', 'firmware', 'serial_number', 'ptz_protocol'], 'string', 'max' => 255],
            [['resolution'], 'string', 'max' => 50],
            [['guid'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getDvr(): ActiveQuery
    {
        return $this->hasOne(Dvr::class, ['id' => 'dvr_id']);
    }

    /**
     * @param bool $main
     *
     * @return string|null
     */
    public function getStreamUrl(bool $main = true): ?string
    {
        $dvr = $this->dvr;
        if (!$dvr) return null;

        return match ($dvr->system_type) {
            Dvr::SYSTEM_TRASSIR => $this->stream_main,
            Dvr::SYSTEM_DAHUA => sprintf(
                'rtsp://%s:%s@%s:554/cam/realmonitor?channel=%d&subtype=%d',
                $dvr->username ?? 'admin',
                $dvr->password ?? 'admin',
                $dvr->ip_address,
                $this->channel_no ?? 1,
                $main ? 0 : 1
            ),
            Dvr::SYSTEM_HIKVISION => sprintf(
                'rtsp://%s:%s@%s:554/Streaming/Channels/%d01',
                $dvr->username ?? 'admin',
                $dvr->password ?? 'admin',
                $dvr->ip_address,
                $this->channel_no ?? 1
            ),
            default => null,
        };
    }
}
