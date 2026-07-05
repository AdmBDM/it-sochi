<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $dvr_id
 * @property string|null $scan_type
 * @property string|null $status
 * @property string|null $message
 * @property array|null $details
 * @property string $created_at
 * @property string $updated_at
 */

class ScanLog extends ActiveRecord
{
    public const string TYPE_DVR = 'dvr_scan';
    public const string TYPE_CAMERA = 'camera_scan';
    public const string STATUS_SUCCESS = 'success';
    public const string STATUS_ERROR = 'error';
    public const string STATUS_PARTIAL = 'partial';

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%scan_log}}';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['scan_type', 'status'], 'required'],
            [['scan_type'], 'in', 'range' => [self::TYPE_DVR, self::TYPE_CAMERA]],
            [['status'], 'in', 'range' => [self::STATUS_SUCCESS, self::STATUS_ERROR, self::STATUS_PARTIAL]],
            [['dvr_id'], 'integer'],
            [['message'], 'string'],
            [['details'], 'safe'],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getDvr(): ActiveQuery
    {
        return $this->hasOne(Dvr::class, ['id' => 'dvr_id']);
    }
}
