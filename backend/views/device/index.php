<?php

use common\models\DeviceBrand;
use common\models\DeviceModel;
use common\models\DeviceStatus;
use common\models\DeviceType;
use yii\bootstrap5\Modal;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var common\models\search\DeviceSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Учёт устройств';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="device-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= Html::a('Добавить устройство', ['create'], ['class' => 'btn btn-success']) ?></p>

    <?php Pjax::begin(['id' => 'device-grid-pjax']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                'options' => ['style'=>'width: 110px; a:not(:last-child) {margin-right: 7px;}'],
                'template' => '{change-status} {view} {update} {delete}',
                'buttons' => [
                    'change-status' => function ($url, $model, $key) {
                        return Html::a(
                        // SVG-иконка "refresh" из Font Awesome 6
                            '<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:.875em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                <path fill="currentColor" d="M129.9 292.5C143.2 199.5 223.3 128 320 128C373 128 421 149.5 455.8 184.2C456 184.4 456.2 184.6 456.4 184.8L464 192L416.1 192C398.4 192 384.1 206.3 384.1 224C384.1 241.7 398.4 256 416.1 256L544.1 256C561.8 256 576.1 241.7 576.1 224L576.1 96C576.1 78.3 561.8 64 544.1 64C526.4 64 512.1 78.3 512.1 96L512.1 149.4L500.8 138.7C454.5 92.6 390.5 64 320 64C191 64 84.3 159.4 66.6 283.5C64.1 301 76.2 317.2 93.7 319.7C111.2 322.2 127.4 310 129.9 292.6zM573.4 356.5C575.9 339 563.7 322.8 546.3 320.3C528.9 317.8 512.6 330 510.1 347.4C496.8 440.4 416.7 511.9 320 511.9C267 511.9 219 490.4 184.2 455.7C184 455.5 183.8 455.3 183.6 455.1L176 447.9L223.9 447.9C241.6 447.9 255.9 433.6 255.9 415.9C255.9 398.2 241.6 383.9 223.9 383.9L96 384C87.5 384 79.3 387.4 73.3 393.5C67.3 399.6 63.9 407.7 64 416.3L65 543.3C65.1 561 79.6 575.2 97.3 575C115 574.8 129.2 560.4 129 542.7L128.6 491.2L139.3 501.3C185.6 547.4 249.5 576 320 576C449 576 555.7 480.6 573.4 356.5z"/>
            </svg>',
                            ['device/change-status', 'id' => $model->id],
                            [
                                'title' => 'Изменить статус или рабочее место',
                                'class' => 'btn-change-status',
                                'data-id' => $model->id,
                                'data-pjax' => '0',
                            ]
                        );
                    },
                ],
            ],

//            'id',
            [
                'attribute' => 'deviceTypeName',
                'label' => 'Тип',
                'value' => function ($model) {
                    return $model->model->type->name ?? null;
                },
                'filter' => ArrayHelper::map(
                    DeviceType::find()->orderBy('name')->all(), 'name', 'name'
                ),
            ],
            [
                'attribute' => 'deviceBrandName',
                'label' => 'Бренд',
                'value' => function ($model) {
                    return $model->model->brand->name ?? null;
                },
                'filter' => ArrayHelper::map(
                    DeviceBrand::find()->orderBy('name')->all(), 'name', 'name'
                ),
            ],
            [
                'attribute' => 'deviceModelName',
                'label' => 'Модель',
                'value' => function ($model) {
                    return $model->model->name ?? null;
                },
                'filter' => ArrayHelper::map(
                    DeviceModel::find()->orderBy('name')->all(), 'name', 'name'
                ),
            ],
            'serial_number',
            'inventory_number',
            [
                'attribute' => 'employeeFullName',
                'label' => 'Сотрудник',
                'value' => function ($model) {
                    return $model->workplace->employee->famIO ?? null;
                },
                'filter' => Html::activeTextInput($searchModel, 'employeeFullName', [
                    'class' => 'form-control',
                    'placeholder' => 'ФИО'
                ]),
            ],
            [
                'attribute' => 'name',
                'options' => ['style'=>'width: 150px;'],
            ],
            [
                'attribute' => 'status_id',
                'value' => fn($model) => $model->status->name ?? null,
                'filter' => ArrayHelper::map(DeviceStatus::find()->all(), 'id', 'name'),
            ],
//            'updated_at',
        ],
    ]); ?>
    <?php Pjax::end(); ?>

</div>

<?php
Modal::begin([
    'id' => 'changeStatusModal',
    'title' => 'Изменение статуса или рабочего места',
    'size' => Modal::SIZE_DEFAULT,
]);
echo '<div id="changeStatusModalContent"></div>';
Modal::end();

$changeUrl = Url::to(['device/change-status']);
$script = <<<JS
// Клик по кнопке "изменить статус / рабочее место"
$(document).on('click', '.btn-change-status', function (e) {
    e.preventDefault();
    var id = $(this).data('id');

    if (window.innerWidth < 768) {
        // fallback на отдельную страницу для мобильных
        window.location.href = '{$changeUrl}?id=' + id;
        return;
    }

    // открыть модалку и загрузить форму через AJAX
    $('#changeStatusModal').modal('show')
        .find('#changeStatusModalContent')
        .load('{$changeUrl}?id=' + id);
});

// Отправка формы внутри модалки
$(document).off('submit', '#change-status-form');
$(document).on('submit', '#change-status-form', function (e) {
    e.preventDefault();
    var \$form = $(this);

    $.ajax({
        url: \$form.attr('action'),
        type: \$form.attr('method') || 'post',
        data: \$form.serialize(),
        success: function (response) {
            if (response === 'success') {
                // закрыть модалку и обновить GridView
                $('#changeStatusModal').modal('hide');
                $.pjax.reload({container:'#device-grid-pjax'});
            } else {
                // вставляем HTML формы с ошибкой обратно в модалку
                $('#changeStatusModalContent').html(response);
            }
        },
        error: function(xhr) {
            var msg = 'Ошибка: ' + (xhr.statusText || xhr.status);
            $('#changeStatusModalContent').prepend('<div class="alert alert-danger">'+msg+'</div>');
        }
    });

    return false;
});
JS;

$this->registerJs($script, View::POS_READY);
?>
