<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Movement $model */
/** @var common\models\Device $devices */
/** @var common\models\Employee $employees */
/** @var common\models\Workplace $workplaces */
/** @var common\models\Organization $organizations */
/** @var common\models\Department $departments */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="movement-form">

    <?php $form = ActiveForm::begin(); ?>

<!--    --><?php //= $form->field($model, 'device_id')->textInput() ?>
    <?= $form->field($model, 'device_id')->dropDownList($devices, ['prompt' => 'Выберите устройство']) ?>

    <?= $form->field($model, 'from_workplace_id')->textInput() ?>
<!--    --><?php //= $form->field($model, 'from_workplace_id')->dropDownList($workplaces, ['prompt' => 'Откуда перемещается']) ?>

    <?= $form->field($model, 'to_workplace_id')->textInput() ?>
<!--    --><?php //= $form->field($model, 'to_workplace_id')->dropDownList($workplaces, ['prompt' => 'Куда перемещается']) ?>

    <?= $form->field($model, 'moved_at')->textInput() ?>

    <?= $form->field($model, 'moved_by_user_id')->textInput() ?>
<!--    --><?php //= $form->field($model, 'moved_by_user_id')->dropDownList($employees, ['prompt' => 'Выберите сотрудника']) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

<!--    --><?php //= $form->field($model, 'organization_id')->dropDownList($organizations, ['prompt' => 'Организация']) ?>

<!--    --><?php //= $form->field($model, 'department_id')->dropDownList($departments, ['prompt' => 'Отдел']) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
