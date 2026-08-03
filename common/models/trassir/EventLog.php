<?php

namespace common\models\trassir;

/**
 * Таблица public.event_log.
 *
 * @property int    $id
 * @property int    $event_type
 * @property int    $ts
 * @property string $p1
 * @property string $p2
 */
class EventLog extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'public.event_log';
    }

}
