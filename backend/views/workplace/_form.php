<?php

use common\models\Department;
use common\models\Employee;
use kartik\depdrop\DepDrop;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var $model common\models\Workplace */

?>

<div class="workplace-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'employee_id')->dropDownList(
        ArrayHelper::map(Employee::find()->orderBy('full_name')->all(), 'id', fn($e) => $e->full_name),
        ['prompt' => 'Выберите сотрудника']
    ) ?>

    <?= $form->field($model, 'department_id')->dropDownList(
        ArrayHelper::map(Department::find()->all(), 'id', 'name'),
        [
            'prompt' => 'Выберите подразделение',
            'id' => 'workplace-department_id', // обязательно
        ]
    ) ?>

    <?= $form->field($model, 'location_id')->dropDownList(
        [], // изначально пусто
        ['prompt' => 'Сначала выберите подразделение', 'id' => 'workplace-location_id']
    ) ?>


    <?= $form->field($model, 'comment')->textarea(['rows' => 3]) ?>

    <div class="form-group mt-3">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
//$url = Url::to(['/workplace/location-list']);
//$csrfParam = Yii::$app->request->csrfParam;
//$csrfToken = Yii::$app->request->csrfToken;
//
//$script = <<<JS
//$('#workplace-department_id').on('change', function() {
//    const deptId = $(this).val();
//    const locSelect = $('#workplace-location_id');
//
//    locSelect.html('<option value="">Загрузка...</option>');
//
//    $.ajax({
//        url: '$url',
//        method: 'POST',
//        dataType: 'json',
//        data: {
//            '$csrfParam': '$csrfToken',
//            department_id: deptId
//        },
//        success: function(response) {
//            let options = '<option value="">Выберите локацию</option>';
//            response.forEach(function(item) {
//                options += '<option value="' + item.id + '">' + item.name + '</option>';
//            });
//            locSelect.html(options);
//        },
//        error: function(xhr) {
//            console.error('AJAX Error:', xhr.status, xhr.responseText);
//            locSelect.html('<option value="">Ошибка загрузки</option>');
//        }
//    });
//});
//JS;
//
//$this->registerJs($script);
//?>

<?php
$initialDepartmentId = $model->department_id;
$initialLocationId = $model->location_id;
$url = Url::to(['/workplace/location-list']);
$csrfParam = Yii::$app->request->csrfParam;
$csrfToken = Yii::$app->request->csrfToken;

$script = <<<JS
function loadLocations(deptId, selectedId = null) {
    const locSelect = $('#workplace-location_id');
    locSelect.html('<option value="">Загрузка...</option>');

    $.ajax({
        url: '$url',
        method: 'POST',
        dataType: 'json',
        data: {
            department_id: deptId,
            '$csrfParam': '$csrfToken'
        },
        success: function(response) {
            let options = '<option value="">Выберите локацию</option>';
            response.forEach(function(item) {
                let selected = selectedId == item.id ? ' selected' : '';
                options += '<option value="' + item.id + '"' + selected + '>' + item.name + '</option>';
            });
            locSelect.html(options);
        },
        error: function(xhr) {
            console.error('Ошибка AJAX:', xhr.status, xhr.responseText);
            locSelect.html('<option value="">Ошибка загрузки</option>');
        }
    });
}

// Обработка изменения department_id
$('#workplace-department_id').on('change', function() {
    const deptId = $(this).val();
    loadLocations(deptId);
});

// Если есть начальный department_id и location_id — подгружаем
if ('$initialDepartmentId' && '$initialLocationId') {
    loadLocations('$initialDepartmentId', '$initialLocationId');
}
JS;

$this->registerJs($script);
?>

