<?php

declare(strict_types=1);

use common\models\ReferenceItem;
use common\models\search\ReferenceItemSearch;
use yii\bootstrap5\Modal;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var ReferenceItemSearch $searchModel
 * @var ActiveDataProvider $dataProvider
 * @var ReferenceItem|null $selectedNode
 * @var array<int|null, ReferenceItem[]> $groupedTree
 * @var $rootNodes
 * @var array<int,bool> $expandedNodes
 */

$this->title = 'Универсальный классификатор';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="reference-index">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0"><?= Html::encode($this->title) ?></h1>

        <div>
            <?= Html::a(
                    'Создать',
                    ['create', 'parent_id' => $selectedNode?->id],
                    [
                            'class' => 'btn btn-success',
                            'id' => 'btn-create-reference',
                    ]
            ) ?>

            <?php if ($selectedNode !== null): ?>
                <?= Html::a(
                        'Редактировать',
                        ['update', 'id' => $selectedNode->id],
                        [
                                'class' => 'btn btn-primary',
                                'id' => 'btn-update-reference',
                        ]
                ) ?>
                <?php if ($selectedNode->is_deleted): ?>

                    <?= Html::a(
                            'Восстановить',
                            ['restore', 'id' => $selectedNode->id],
                            [
                                    'class' => 'btn btn-success',
                                    'id' => 'btn-restore-reference',
                                    'data-name' => $selectedNode->name,
                            ]
                    ) ?>

                <?php else: ?>

                    <?= Html::a(
                            'Удалить',
                            ['delete', 'id' => $selectedNode->id],
                            [
                                    'class' => 'btn btn-danger',
                                    'id' => 'btn-delete-reference',
                                    'data-name' => $selectedNode->name,
                            ]
                    ) ?>

                <?php endif; ?>
            <?php else: ?>
                <?= Html::button(
                        'Редактировать',
                        [
                                'class' => 'btn btn-primary',
                                'disabled' => true,
                        ]
                ) ?>
                <?= Html::button(
                        'Удалить',
                        [
                                'class' => 'btn btn-danger',
                                'disabled' => true,
                        ]
                ) ?>

            <?php endif; ?>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center alert alert-secondary">

        <div>
            <strong>Выбранный узел:</strong>
            <?= $selectedNode === null
                    ? 'Корневой уровень'
                    : Html::encode($selectedNode->name) ?>
        </div>

        <div>
            <?= Html::beginForm(['index'], 'get') ?>

            <?= Html::hiddenInput('id', $selectedNode?->id) ?>

            <div class="form-check mb-0">
                <?= Html::activeCheckbox(
                        $searchModel,
                        'showDeleted',
                        [
                                'label' => 'Показывать удалённые',
                                'onchange' => 'this.form.submit()',
                        ]
                ) ?>
            </div>

            <?= Html::endForm() ?>
        </div>

    </div>

    <div class="row">

        <div class="col-lg-4 mb-3">
            <?= $this->render('_tree', [
                    'selectedNode' => $selectedNode,
                    'rootNodes' => $rootNodes,
                    'groupedTree' => $groupedTree,
                    'expandedNodes' => $expandedNodes,
            ]) ?>
        </div>

        <div class="col-lg-8">

            <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,

                    'rowOptions' => static function (ReferenceItem $model): array {
                        if ($model->is_deleted) {
                            return ['class' => 'table-danger text-decoration-line-through',];
                        }
                        return [];
                    },
                    'columns' => [
                            [
                                    'class' => ActionColumn::class,
                                    'header' => '',
                                    'template' => '{up} {down} {view}',
                                    'contentOptions' => [
                                            'class' => 'text-nowrap text-center',
                                            'style' => 'width:70px',
                                    ],
                                    'buttons' => [
                                            'up' => static function (string $url, $model): string {
                                                return Html::a(
                                                        '↑',
                                                        [
                                                                'move-up',
                                                                'id' => $model->id,
                                                        ],
                                                        [
                                                                'class' => 'btn btn-sm btn-outline-secondary',
                                                                'title' => 'Переместить вверх',
                                                        ]
                                                );
                                            },

                                            'down' => static function (string $url, $model): string {
                                                return Html::a(
                                                        '↓',
                                                        [
                                                                'move-down',
                                                                'id' => $model->id,
                                                        ],
                                                        [
                                                                'class' => 'btn btn-sm btn-outline-secondary',
                                                                'title' => 'Переместить вниз',
                                                        ]
                                                );
                                            },

                                            'view' => static function (string $url, ReferenceItem $model): string {
                                                return Html::a(
                                                        '<i class="bi bi-eye"></i>',
                                                        ['view', 'id' => $model->id],
                                                        [
                                                                'class' => 'btn btn-sm btn-outline-primary',
                                                                'title' => 'Просмотр',
                                                        ]
                                                );
                                            },
                                    ],
                            ],

                            [
                                    'attribute' => 'code',
                                    'label' => 'Код',
                            ],
                            [
                                    'attribute' => 'name',
                                    'label' => 'Наименование',
                            ],
                            [
                                    'attribute' => 'type_id',
                                    'label' => 'Тип',
                                    'value' => static fn (ReferenceItem $model): string => $model->type?->name ?? '',
                            ],
//                            [
//                                    'attribute' => 'sort_order',
//                                    'label' => 'Порядок',
//                            ],
                            [
                                    'attribute' => 'is_active',
                                    'label' => 'Активна',
                                    'format' => 'boolean',
                            ],
                    ],
            ]) ?>

        </div>

    </div>

</div>

<div class="modal fade" id="reference-modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>

<?php
Modal::begin([
        'id' => 'reference-selector-modal',
        'title' => '',
        'size' => Modal::SIZE_LARGE,
]);
echo '<div id="reference-selector-content" class="bg-light p-3"></div>';
Modal::end();
?>

<?php
$this->registerJs(<<<JS

$(document).on('click', '#btn-create-reference', function (e) {
    e.preventDefault();

    $('#reference-modal .modal-content').load($(this).attr('href'), function () {
        window.initReferenceForm();
        $('#reference-modal').modal('show');
    });
});

$(document).on('click', '#btn-update-reference', function (e) {
    e.preventDefault();

    $('#reference-modal .modal-content').load($(this).attr('href'), function () {
        window.initReferenceForm();
        $('#reference-modal').modal('show');
    });
});

$(document).on('click', '#btn-delete-reference', function (e) {

    e.preventDefault();

    const link = $(this);

    if (!confirm(
        'Удалить "' + link.data('name') + '"?'
    )) {
        return;
    }

    $.ajax({

        url: link.attr('href'),
        type: 'POST',

        success: function (response) {

            if (typeof response === 'object' && response.success) {

                // $.pjax.reload({container: '#reference-grid-pjax'});

                location.reload();
            }

        }

    });

});

$(document).on('click', '#btn-restore-reference', function (e) {

    e.preventDefault();

    const link = $(this);

    if (!confirm(
        'Восстановить "' + link.data('name') + '"?'
    )) {
        return;
    }

    $.ajax({

        url: link.attr('href'),
        type: 'POST',

        success: function (response) {

            if (typeof response === 'object' && response.success) {

                location.reload();
            }

        }

    });

});

$(document).on('beforeSubmit', '#reference-form', function (e) {

    e.preventDefault();

    const form = $(this);

    $.ajax({

        url: form.attr('action'),
        type: form.attr('method'),
        data: form.serialize(),

        success: function (response) {

            if (typeof response === 'object' && response.success) {

                bootstrap.Modal
                    .getInstance(document.getElementById('reference-modal'))
                    .hide();

                // $.pjax.reload({container: '#reference-grid-pjax'});
                location.reload();

                return;
            }
            
            $('#reference-modal .modal-content').html(response);

        }

    });

    return false;

});

JS);
?>
