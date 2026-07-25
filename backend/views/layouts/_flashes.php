<?php

declare(strict_types=1);

use yii\helpers\Html;

/**
 * Универсальный вывод flash-сообщений.
 */

const FLASH_CLASSES = [
    'success' => 'alert-success',
    'error'   => 'alert-danger',
    'warning' => 'alert-warning',
    'info'    => 'alert-info',
];

foreach (Yii::$app->session->getAllFlashes() as $type => $messages) {

    $messages = (array)$messages;

    foreach ($messages as $message) {

        if ($message === null || $message === '') {
            continue;
        }

        ?>
        <div class="alert <?= Html::encode(FLASH_CLASSES[$type] ?? 'alert-secondary') ?> alert-dismissible fade show"
             role="alert">

            <?= Html::encode((string)$message) ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Закрыть">
            </button>

        </div>
        <?php
    }
}
