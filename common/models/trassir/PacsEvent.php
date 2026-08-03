<?php

namespace common\models\trassir;

use yii\db\ActiveQuery;

/**
 * Таблица pacs._events.
 *
 * @property int $event_id
 * @property int $event_ts
 * @property int $event_ts_with_device_offset
 */
class PacsEvent extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'pacs._events';
    }

    public static function findByPeriod(int $from, int $to): ActiveQuery
    {
        return static::find()
            ->andWhere(['>=', 'event_ts', $from])
            ->andWhere(['<=', 'event_ts', $to])
            ->orderBy(['event_ts' => SORT_ASC]);
    }
}
