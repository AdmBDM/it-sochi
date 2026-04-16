<?php

use common\models\Movement;
use yii\bootstrap5\Modal;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\MovementSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Перемещения устройств';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="movement-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(['timeout' => 5000]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
//            [
//                'class' => 'yii\grid\ActionColumn',
//                'options' => ['style'=>'width: 70px; a:not(:last-child) {margin-right: 10px;}'],
//                'template' => '{view} {delete}',
//            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view-modal} {history-modal} {delete}',
                'buttons' => [
                    /**
                     * Кнопка открытия модального окна с деталями перемещения.
                     *
                     * @param string $url URL для действия
                     * @param Movement $model текущая модель
                     * @return string HTML-код кнопки
                     */
                    'view-modal' => function ($url, $model) {
                        return Html::a(
                            '<i class="bi bi-eye"></i>',
                            '#',
                            [
                                'class' => 'btn btn-sm btn-outline-primary view-movement',
                                'title' => 'Просмотр перемещения',
                                'data-id' => $model->id,
                            ]
                        );
                    },
                    /**
                     * Кнопка истории перемещений устройства.
                     */
                    'history-modal' => function ($url, $model) {
                        return Html::a(
                            '<i class="bi bi-clock-history"></i>',
                            '#',
                            [
                                'class' => 'btn btn-sm btn-outline-secondary history-movement',
                                'title' => 'История перемещений',
                                'data-device-id' => $model->device_id,
                            ]
                        );
                    },

                ],
            ],

//            'id',
//            [
//                'attribute' => 'deviceName',
//                'value' => 'device.name',
//                'label' => 'Устройство',
//            ],
            [
                'attribute' => 'deviceName',
                'value' => function($model) {
                    return $model->device ? $model->device->fullName : null;
                },
                'label' => 'Устройство',
            ],
//            [
//                'attribute' => 'oldValueName',
//                'value' => function($model) { return $model->oldValue; },
//                'label' => 'Старое значение',
//            ],
            [
                'attribute' => 'newValueName',
                'options' => ['style'=>'width: 170px;'],
                'value' => function($model) { return $model->newValue; },
                'label' => 'Новое значение',
            ],
            [
                'attribute' => 'moved_at',
                'format' => 'datetime',
                'options' => ['style'=>'width: 200px;'],
            ],
            [
                'attribute' => 'type_change',
                'options' => ['style'=>'width: 90px;'],
                'filter' => ['status' => 'Статус', 'place' => 'Рабочее место'],
            ],
            [
                'attribute' => 'employeeName',
                'options' => ['style'=>'width: 150px;'],
//                'value' => 'employee.FamIO',
                'format' => 'raw',
                'value' => function($model) {
                    if (!$model->employee) {
                        return null;
                    }

                    $fullName = trim($model->employee->last_name . ' ' . $model->employee->first_name . ' ' . $model->employee->middle_name);
                    $shortName = $model->employee->famIO;

                    return "<span title='{$fullName}'>{$shortName}</span>";
                },
                'label' => 'Перенёс',
            ],
//            'comment:ntext',
// пустая колонка для выравнивания
            [
                'label' => '',
                'format' => 'text',
                'contentOptions' => ['style'=>'white-space: normal;'],
                'value' => function() {return '';},
            ],

        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<?php
/**
 * Bootstrap Modal для отображения детальной информации о перемещении.
 */
Modal::begin([
    'id' => 'movement-modal',
    'title' => '<h5>Информация о перемещении</h5>',
    'size' => Modal::SIZE_LARGE,
]);
echo '<div id="movement-modal-content"></div>';
Modal::end();

/**
 * JS-обработчик клика на кнопку "Просмотр".
 * Выполняет AJAX-загрузку содержимого модального окна.
 */
$this->registerJs(<<<JS
$(document).on('click', '.view-movement', function (e) {
    e.preventDefault();
    const id = $(this).data('id');
    const modal = $('#movement-modal');
    modal.find('#movement-modal-content').html('<div class="p-3 text-center text-muted">Загрузка...</div>');
    modal.modal('show')
        .find('#movement-modal-content')
        .load('/backend/web/movement/view-modal?id=' + id);
});
JS);
?>

<?php
$this->registerJs(<<<JS
// Модал с деталями перемещения
$(document).on('click', '.view-movement', function (e) {
    e.preventDefault();
    const id = $(this).data('id');
    const modal = $('#movement-modal');
    modal.find('#movement-modal-content').html('<div class="p-3 text-center text-muted">Загрузка...</div>');
    modal.modal('show')
        .find('#movement-modal-content')
        .load('/backend/web/movement/view-modal?id=' + id);
});

// Модал с историей устройства
$(document).on('click', '.history-movement', function (e) {
    e.preventDefault();
    const deviceId = $(this).data('device-id');
    const modal = $('#movement-history-modal');
    modal.find('#movement-history-content').html('<div class="p-3 text-center text-muted">Загрузка...</div>');
    modal.modal('show')
        .find('#movement-history-content')
        .load('/movement/history-modal?device_id=' + deviceId);
});
JS);
?>
