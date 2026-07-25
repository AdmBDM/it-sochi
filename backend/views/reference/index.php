<?php

declare(strict_types=1);

use common\models\ReferenceItem;
use common\models\search\ReferenceItemSearch;
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
                    [
                            'create',
                            'parent_id' => $selectedNode?->id,
                    ],
                    [
                            'class' => 'btn btn-success',
                    ]
            ) ?>

            <?php if ($selectedNode !== null): ?>
                <?= Html::a(
                        'Редактировать',
                        ['update', 'id' => $selectedNode->id],
                        ['class' => 'btn btn-primary']
                ) ?>
                <?= Html::a(
                        'Удалить',
                        ['delete', 'id' => $selectedNode->id],
                        [
                                'class' => 'btn btn-danger',
                                'data' => [
                                        'confirm' => 'Пометить элемент как удалённый?',
                                        'method' => 'post',
                                ],
                        ]
                ) ?>

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

    <div class="alert alert-secondary">
        <strong>Выбранный узел:</strong>
        <?= $selectedNode === null
            ? 'Корневой уровень'
            : Html::encode($selectedNode->name) ?>
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
