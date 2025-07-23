<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\DeviceCartridgeType $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="device-cartridge-type-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'device_id')->textInput() ?>

    <?= $form->field($model, 'cartridge_type_id')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'is_active')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
