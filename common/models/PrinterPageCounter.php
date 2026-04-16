<?php

namespace common\models;

// common/models/PrinterPageCounter.php
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

class PrinterPageCounter extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%printer_page_counters}}';
    }

    /**
     * @return ActiveQuery
     */
    public function getDevice(): ActiveQuery
    {
        return $this->hasOne(Device::class, ['id' => 'device_id']);
    }

    // Расчёт месячной нагрузки

    /**
     * @param int $deviceId
     * @param int $months
     *
     * @return int|null
     */
    public static function getMonthlyLoad(int $deviceId, int $months = 3): ?int
    {
        $first = self::find()
            ->where(['device_id' => $deviceId])
            ->andWhere(['>=', 'captured_at', new Expression("NOW() - INTERVAL '$months months'")])
            ->orderBy('captured_at ASC')
            ->one();

        $last = self::find()
            ->where(['device_id' => $deviceId])
            ->orderBy('captured_at DESC')
            ->one();

        if (!$first || !$last || $first->id === $last->id) return null;

        return $last->total_pages - $first->total_pages;
    }
}
