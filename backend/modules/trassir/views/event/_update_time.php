<?php

use yii\bootstrap5\Html;

/** @var int $eventTs */
/** @var int $offsetTs */

?>

<?= Html::hiddenInput(
    'oldEventTs',
    $eventTs,
    [
        'id' => 'old-event-ts',
    ]
) ?>

<div class="mb-3">

    <label class="form-label">
        event_ts
    </label>

    <?= Html::input(
        'datetime-local',
        'eventTs',
        date('Y-m-d\TH:i:s', intdiv($eventTs, 1000000)),
        [
            'class' => 'form-control',
            'id' => 'event-ts',
            'step' => 1,
        ]
    ) ?>

</div>

<div class="mb-3">

    <label class="form-label">
        event_ts_with_device_offset
    </label>

    <?= Html::input(
        'datetime-local',
        'offsetTs',
        date('Y-m-d\TH:i:s', intdiv($offsetTs, 1000000)),
        [
            'class' => 'form-control',
            'id' => 'offset-ts',
            'step' => 1,
        ]
    ) ?>

</div>

<div class="text-end">

    <button
        type="button"
        class="btn btn-primary"
        id="save-event-time">

        Сохранить

    </button>

</div>
