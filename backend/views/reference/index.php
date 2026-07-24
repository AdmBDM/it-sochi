<?php

declare(strict_types=1);

use common\models\ReferenceItem;
use common\models\search\SearchReferenceItem;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var SearchReferenceItem $searchModel
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
                            [
                                    'attribute' => 'sort_order',
                                    'label' => 'Порядок',
                            ],
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
