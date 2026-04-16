<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\PrinterRepair */
/* @var $printers common\models\Device[] */
?>

    <div class="printer-repair-form">

        <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'device_id')->dropDownList(
                    ArrayHelper::map($printers, 'id', function($p) {
                        return $p->name . ' (' . $p->model->brand->name . ' ' . $p->model->name . ')';
                    }),
                    ['prompt' => 'Выберите принтер']
                ) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'service_center')->textInput(['maxlength' => true, 'value' => $model->isNewRecord ? 'ИП Смелков' : $model->service_center]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'started_at')->input('date') ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'finished_at')->input('date') ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'cost')->input('number', ['step' => '0.01', 'min' => 0]) ?>
            </div>
        </div>

        <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>

        <!-- Динамический список запчастей -->
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Заменённые запчасти</span>
                <?= Html::button('<i class="fas fa-plus"></i> Добавить', [
                    'class' => 'btn btn-sm btn-success',
                    'id' => 'add-part-btn'
                ]) ?>
            </div>
            <div class="card-body" id="parts-container">
                <?php
                $parts = !empty($model->partsInput) ? $model->partsInput : [''];
                foreach ($parts as $i => $part):
                    ?>
                    <div class="input-group mb-2 part-row">
                        <?= Html::textInput("PrinterRepair[partsInput][]", $part, [
                            'class' => 'form-control',
                            'placeholder' => 'Название запчасти'
                        ]) ?>
                        <button type="button" class="btn btn-outline-danger remove-part">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?= $form->field($model, 'document_number')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

<?php
$js = <<<JS
$('#add-part-btn').on('click', function() {
    var row = $('<div class="input-group mb-2 part-row">' +
        '<input type="text" name="PrinterRepair[partsInput][]" class="form-control" placeholder="Название запчасти">' +
        '<button type="button" class="btn btn-outline-danger remove-part"><i class="fas fa-times"></i></button>' +
    '</div>');
    $('#parts-container').append(row);
});

$(document).on('click', '.remove-part', function() {
    if ($('.part-row').length > 1) {
        $(this).closest('.part-row').remove();
    } else {
        $(this).closest('.part-row').find('input').val('');
    }
});
JS;
$this->registerJs($js);
?>