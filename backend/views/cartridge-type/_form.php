<?php

use common\models\CartridgeType;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\CartridgeType $model */
/** @var yii\widgets\ActiveForm $form */


?>

<div class="cartridge-type-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'color')->dropDownList(CartridgeType::getColorList()) ?>

    <?= $form->field($model, 'initial_quantity')->textInput() ?>

    <?= $form->field($model, 'is_active')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs(<<<JS
    document.getElementById("cartridgetype-name")?.focus();
JS);
?>
