<?php

use common\models\DeviceModel;
use common\models\DeviceStatus;
use common\models\Workplace;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Device $model */
/** @var yii\widgets\ActiveForm $form */

?>

<div class="device-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'model_id')->dropDownList(
        ArrayHelper::map(DeviceModel::find()->with(['brand', 'type'])->all(), 'id', function($m) {
            return "{$m->type->name} / {$m->brand->name} / {$m->name}";
        }),
        ['prompt' => 'Выберите модель']
    ) ?>

    <?= $form->field($model, 'status_id')->dropDownList(
        ArrayHelper::map(DeviceStatus::find()->all(), 'id', 'name'),
        ['prompt' => 'Выберите статус']
    ) ?>

    <?= $form->field($model, 'workplace_id')->dropDownList(
        ArrayHelper::map(Workplace::find()->with(['location', 'employee'])->all(), 'id', function($w) {
            return "{$w->employee->fullName} / {$w->location->name}";
        }),
        ['prompt' => 'Выберите рабочее место']
    ) ?>

    <?= $form->field($model, 'serial_number')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'inventory_number')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'comment')->textarea(['rows' => 3]) ?>

    <div class="form-group mt-3">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
