<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Building;

/** @var yii\web\View $this */
/** @var common\models\Location $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="location-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'building_id')->dropDownList(
        ArrayHelper::map(Building::find()->all(), 'id', 'name'),
        ['prompt' => 'Выберите здание']
    ) ?>

    <?= $form->field($model, 'floor')->textInput() ?>
    <?= $form->field($model, 'room')->textInput() ?>

    <div class="form-group mt-3">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
