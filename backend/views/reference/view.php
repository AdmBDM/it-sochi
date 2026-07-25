<?php

declare(strict_types=1);

use common\models\ReferenceItem;
use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var ReferenceItem $model
 */

$this->title = $model->name;

$this->params['breadcrumbs'][] = [
    'label' => 'Универсальный классификатор',
    'url' => ['index'],
];

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="reference-view">

    <h1><?= Html::encode($model->name) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [

            'id',

            [
                'attribute' => 'parent_id',
                'value' => $model->parent?->name,
            ],

            [
                'attribute' => 'type_id',
                'value' => $model->type?->name,
            ],

            'code',
            'name',
            'description:ntext',
            'sort_order:boolean',
            'is_active:boolean',
        ],
    ]) ?>

</div>
