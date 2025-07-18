<?php

/** @var $model     common\models\Workplace */

use common\models\Department;
use common\models\Employee;
use common\models\Location;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="workplace-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'employee_id')->dropDownList(
//        ArrayHelper::map(Employee::find()->all(), 'id', fn($e) => $e->lastname . ' ' . $e->firstname),
        ArrayHelper::map(Employee::find()->orderBy('full_name')->all(), 'id', fn($e) => $e->full_name),
        ['prompt' => 'Выберите сотрудника']
    ) ?>

    <?= $form->field($model, 'department_id')->dropDownList(
        ArrayHelper::map(Department::find()->all(), 'id', 'name'),
        ['prompt' => 'Выберите подразделение']
    ) ?>

<!--    --><?php //= $form->field($model, 'location_id')->dropDownList(
//        ArrayHelper::map(Location::find()->all(), 'id', 'name'),
//        ['prompt' => 'Выберите локацию']
//    ) ?>
    <?= $form->field($model, 'location_id')->dropDownList(
        ArrayHelper::map(
            Location::find()->with('building')->all(),
            'id',
            fn($loc) => $loc->building->name . ' — эт.' . $loc->floor . ($loc->room ? ' — ' . $loc->room : '')
        ),
        ['prompt' => 'Выберите локацию']
    ) ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 3]) ?>

    <div class="form-group mt-3">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
