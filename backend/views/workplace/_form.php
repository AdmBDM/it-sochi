<?php

use common\models\Building;
use common\models\Department;
use common\models\Employee;
use common\models\Location;
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

<!--    --><?php //= $form->field($model, 'location_id')->dropDownList(
//        [], // изначально пусто
//        ['prompt' => 'Сначала выберите подразделение', 'id' => 'workplace-location_id']
//    ) ?>
    <?= $form->field($model, 'building_id')->dropDownList(
            ArrayHelper::map(
                    Building::find()
                            ->orderBy('name')
                            ->all(),
                    'id',
                    'name'
            ),
            [
                    'prompt' => 'Выберите здание',
                    'id' => 'workplace-building_id',
            ]
    ) ?>

    <?= $form->field($model, 'room')->dropDownList(
            [],
            [
                    'prompt' => 'Сначала выберите подразделение',
                    'id' => 'workplace-room',
            ]
    ) ?>

    <?= $form->field($model, 'floor')->dropDownList(
            [],
            [
                    'prompt' => 'Сначала выберите помещение',
                    'id' => 'workplace-floor',
            ]
    ) ?>

    <?= Html::activeHiddenInput($model, 'location_id', [
            'id' => 'workplace-location_id'
    ]) ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 3]) ?>

    <div class="form-group mt-3">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
//$initialDepartmentId = $model->department_id;
//$initialLocationId = $model->location_id;
//$url = Url::to(['/workplace/location-list']);
//$csrfParam = Yii::$app->request->csrfParam;
//$csrfToken = Yii::$app->request->csrfToken;
//
//$script = <<<JS
//function loadLocations(deptId, selectedId = null) {
//    const locSelect = $('#workplace-location_id');
//    locSelect.html('<option value="">Загрузка...</option>');
//
//    $.ajax({
//        url: '$url',
//        method: 'POST',
//        dataType: 'json',
//        data: {
//            department_id: deptId,
//            '$csrfParam': '$csrfToken'
//        },
//        success: function(response) {
//            let options = '<option value="">Выберите локацию</option>';
//            response.forEach(function(item) {
//                let selected = selectedId == item.id ? ' selected' : '';
//                options += '<option value="' + item.id + '"' + selected + '>' + item.name + '</option>';
//            });
//            locSelect.html(options);
//        },
//        error: function(xhr) {
//            console.error('Ошибка AJAX:', xhr.status, xhr.responseText);
//            locSelect.html('<option value="">Ошибка загрузки</option>');
//        }
//    });
//}
//
//// Обработка изменения department_id
//$('#workplace-department_id').on('change', function() {
//    const deptId = $(this).val();
//    loadLocations(deptId);
//});
//
//// Если есть начальный department_id и location_id — подгружаем
//if ('$initialDepartmentId' && '$initialLocationId') {
//    loadLocations('$initialDepartmentId', '$initialLocationId');
//}
//JS;
//
//$this->registerJs($script);
?>

<?php

$roomUrl = Url::to(['/workplace/room-list']);
$floorUrl = Url::to(['/workplace/floor-list']);

$csrfParam = Yii::$app->request->csrfParam;
$csrfToken = Yii::$app->request->csrfToken;

$building = $model->building_id;
$department = $model->department_id;
$room = $model->room;
$location = $model->location_id;

$script = <<<JS

function clearRooms()
{
    $('#workplace-room')
        .html('<option value="">Выберите помещение</option>');

    clearFloors();
}

function clearFloors()
{
    $('#workplace-floor')
        .html('<option value="">Выберите этаж</option>');

    $('#workplace-location_id').val('');
}

function loadRooms(selectedRoom = null)
{
    const building = $('#workplace-building_id').val();
    const department = $('#workplace-department_id').val();

    clearRooms();

    // if (!building || !department)
    if (!building)
        return;

    $.post('$roomUrl',{

        building_id: building,
        department_id: department,

        '$csrfParam':'$csrfToken'

    },function(items){

        let html='<option value="">Выберите помещение</option>';

        items.forEach(function(item){

            const selected =
                selectedRoom == item.id ? ' selected':'';

            html += '<option value="'+item.id+'"'+selected+'>'+item.name+'</option>';

        });

        $('#workplace-room').html(html);

        if(selectedRoom)
            loadFloors(selectedRoom,$location);

    },'json');
}

function loadFloors(room,selectedLocation=null)
{
    const building=$('#workplace-building_id').val();
    const department=$('#workplace-department_id').val();

    clearFloors();

    if(!room)
        return;

    $.post('$floorUrl',{
        building_id:building,
        department_id:department,
        room:room,
        '$csrfParam':'$csrfToken'
    },function(items){
        let html='<option value="">Выберите этаж</option>';

        items.forEach(function(item){
            const selected =
                selectedLocation == item.id ? ' selected':'';
            html += '<option value="'+item.id+'"'+selected+'>'+item.name+'</option>';
        });

        $('#workplace-floor').html(html);

        if(selectedLocation)
            $('#workplace-location_id').val(selectedLocation);
    },'json');
}

$('#workplace-building_id,#workplace-department_id').change(function(){
// $('#workplace-building_id').change(function(){
    loadRooms();
});

$('#workplace-room').change(function(){
    loadFloors($(this).val());
});

$('#workplace-floor').change(function(){
    $('#workplace-location_id').val($(this).val());
});

if('$building' && '$department')
{
    $('#workplace-building_id').val('$building');
    loadRooms('$room');
}
JS;

$this->registerJs($script);

?>
