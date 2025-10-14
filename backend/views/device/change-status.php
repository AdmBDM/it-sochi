<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Device $model */

?>

<div class="device-change-status-form">

    <?php $form = ActiveForm::begin([
        'id' => 'change-status-form',
        'enableAjaxValidation' => false,
    ]); ?>

    <p><strong>Устройство:</strong> <?= Html::encode($model->name ?? $model->id) ?></p>

    <p>Здесь будет форма выбора статуса и рабочего места (этап 2).</p>

    <div class="form-group">
        <?= Html::a('Отмена', ['index'], ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
