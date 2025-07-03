<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Movement $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="movement-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'device_id')->textInput() ?>

    <?= $form->field($model, 'from_workplace_id')->textInput() ?>

    <?= $form->field($model, 'to_workplace_id')->textInput() ?>

    <?= $form->field($model, 'moved_at')->textInput() ?>

    <?= $form->field($model, 'moved_by_user_id')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
