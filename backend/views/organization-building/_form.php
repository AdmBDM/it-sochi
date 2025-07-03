<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\OrganizationBuilding $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="organization-building-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'organization_id')->textInput() ?>

    <?= $form->field($model, 'building_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
