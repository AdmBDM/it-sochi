<?php

use yii\grid\GridView;
use yii\helpers\Url;

/** @var yii\data\ActiveDataProvider $dataProvider */

$formatTs = static function (?int $value): ?string {

    if ($value === null) {
        return null;
    }

    $sec = intdiv($value, 1000000);
    $usec = $value % 1000000;

    return sprintf(
        '%s.%06d',
        date('d.m.Y H:i:s', $sec),
        $usec
    );
};

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => "{summary}\n{items}\n{pager}",

    'columns' => [

        'event_id',

        [
            'attribute' => 'event_ts',
            'value' => static fn($model) => $formatTs($model->event_ts),
        ],

        [
            'attribute' => 'event_ts_with_device_offset',
            'value' => static fn($model) => $formatTs($model->event_ts_with_device_offset),
        ],

        [
            'class' => yii\grid\ActionColumn::class,
            'template' => '{update-time}',
            'buttons' => [
                'update-time' => static function ($url, $model) {
                    return yii\helpers\Html::a(
                        '<i class="bi bi-pencil"></i>',
                        '#',
                        [
                            'class' => 'btn btn-sm btn-outline-primary js-edit-event-time',
                            'data-url' => Url::to(['update-time']),
                            'data-ts' => $model->event_ts,
                            'data-offset-ts' => $model->event_ts_with_device_offset,
                            'title' => 'Изменить время',
                        ]
                    );
                },
            ],
            'contentOptions' => [
                'style' => 'width:60px;text-align:center',
            ],
        ],

    ],

]);
