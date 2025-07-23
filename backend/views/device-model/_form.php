<?php

use common\models\DeviceBrand;
use common\models\DeviceType;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\DeviceModel $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="device-model-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'type_id')->dropDownList(
        ArrayHelper::map(
            DeviceType::find()->orderBy('name')->all(), 'id', 'name'
        ),
        ['prompt' => 'Выберите тип...']
    ) ?>

    <?= $form->field($model, 'brand_id')->dropDownList(
        ArrayHelper::map(
            DeviceBrand::find()->orderBy('name')->all(), 'id', 'name'
        ),
        ['prompt' => 'Выберите бренд...']
    ) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

<!--    --><?php //= $form->field($model, 'created_at')->textInput() ?>

<!--    --><?php //= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
