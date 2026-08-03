<?php

use backend\modules\trassir\models\EventSearch;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\modules\trassir\models\EventSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Trassir';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="trassir-event-index">

    <div class="card mb-3">
        <div class="card-body">

            <?php $form = ActiveForm::begin([
                'method' => 'get',
                'options' => [
                    'autocomplete' => 'off',
                ],
            ]); ?>

            <div class="row">

                <div class="col-md-4">
                    <?= $form->field($searchModel, 'user')->dropDownList(
                        EventSearch::getUsers(),
                        ['prompt' => 'Все пользователи']
                    ) ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($searchModel, 'dateFrom')->input('date') ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($searchModel, 'dateTo')->input('date') ?>
                </div>

                <div class="col-md-2 d-flex align-items-end pb-3">

                    <?= Html::submitButton(
                        'Найти',
                        ['class' => 'btn btn-primary me-2']
                    ) ?>

                    <?= Html::a(
                        'Сбросить',
                        ['index'],
                        ['class' => 'btn btn-secondary']
                    ) ?>

                </div>

            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>

    <?php
    $formatTs = static function (?int $value): ?string {

        if ($value === null) {
            return null;
        }

        $sec = intdiv($value, 1000000);
        $usec = $value % 1000000;

        return sprintf(
                '%s.%06d',
                date('d.m.Y H:i:s', $sec),
                $usec
        );
    };
    ?>

    <div class="row">

        <div class="col-lg-6">

            <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => "{summary}\n{items}\n{pager}",
                    'options' => ['id' => 'event-log-grid',],
                    'tableOptions' => ['class' => 'table table-striped table-bordered table-hover',],
                    'columns' => [

                            [
                                    'attribute' => 'id',
                                    'contentOptions' => static function ($model) {
                                        return [
                                                'class' => 'event-row',
                                                'data-id' => $model->id,
                                                'data-ts' => $model->ts,
                                                'style' => 'width:90px;cursor:pointer',
                                        ];
                                    },
                            ],

                            [
                                    'attribute' => 'ts',
                                    'label' => 'Дата/время',
                                    'value' => static function ($model) {

                                        $micro = (int)$model->ts;

                                        $sec = intdiv($micro, 1000000);
                                        $usec = $micro % 1000000;

                                        return sprintf(
                                                '%s.%06d',
                                                date('d.m.Y H:i:s', $sec),
                                                $usec
                                        );
                                    },
                                    'contentOptions' => [
                                            'style' => 'white-space:nowrap;width:220px',
                                    ],
                            ],

//                            [
//                                    'attribute' => 'event_type',
//                                    'contentOptions' => [
//                                            'style' => 'width:120px;text-align:center',
//                                    ],
//                            ],

                            [
                                    'attribute' => 'p1',
                                    'label' => 'ФИО',
                            ],

//                            [
//                                    'attribute' => 'p2',
//                                    'label' => 'Идентификатор',
//                                    'contentOptions' => [
//                                            'style' => 'width:180px',
//                                    ],
//                            ],

                    ],
            ]) ?>

        </div>

        <div class="col-lg-6">

            <div id="events-grid">
                <div class="alert alert-secondary mb-0">
                    Выберите запись в левом списке.
                </div>
            </div>

        </div>

    </div>

<?php
$this->registerJs(<<<JS

$(document).on('click', '#event-log-grid .event-row', function () {

    $('#event-log-grid .event-row').removeClass('table-active');
    $(this).addClass('table-active');
    
    const dateFrom = $('#eventsearch-datefrom').val();
    const dateTo   = $('#eventsearch-dateto').val();
    const ts = $(this).data('ts');
    
    $('#events-grid').load('events', {
        from: dateFrom,
        to: dateTo,
        ts: ts
    });
});

$(document).on('click', '.js-edit-event-time', function (e) {
    e.preventDefault();

    const button = $(this);

    $.post(
        button.data('url'),
        {
            eventTs: button.data('ts'),
            offsetTs: button.data('offset-ts')
        },
        function (response) {

            let modal = $('#update-time-modal');
        
            if (!modal.length) {
        
                $('body').append(
                    '<div class="modal fade" id="update-time-modal" tabindex="-1">' +
                        '<div class="modal-dialog">' +
                            '<div class="modal-content">' +
                                '<div class="modal-header">' +
                                    '<h5 class="modal-title">Корректировка времени</h5>' +
                                    '<button type="button" class="btn-close" data-bs-dismiss="modal"></button>' +
                                '</div>' +
                                '<div class="modal-body"></div>' +
                            '</div>' +
                        '</div>' +
                    '</div>'
                );
        
                modal = $('#update-time-modal');
        
            }
        
            modal.find('.modal-body').html(response);
        
            bootstrap.Modal
                .getOrCreateInstance(modal[0])
                .show();
        
        }
    );

});

$(document).on('click', '#save-event-time', function () {
    $.post(
        $('.js-edit-event-time').first().data('url'),
        {
            save: 1,
            oldEventTs: $('#old-event-ts').val(),
            eventTs: $('#event-ts').val(),
            offsetTs: $('#offset-ts').val()
        },
        function (response) {
            response = JSON.parse(response);
        
            if (!response.success) {
                return;
            }
        
            bootstrap.Modal
                .getInstance(document.getElementById('update-time-modal'))
                .hide();
        
            $('.event-row.table-active').trigger('click');
        
        }
    );

});

JS);
?>
</div>
