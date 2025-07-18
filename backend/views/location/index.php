<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Building;

/** @var yii\web\View $this */
/** @var common\models\search\LocationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Локации';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="location-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= Html::a('Добавить локацию', ['create'], ['class' => 'btn btn-success']) ?></p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                'options' => ['style'=>'width: 90px; a:not(:last-child) {margin-right: 7px;}'],
            ],

//            'id',
            [
                'attribute' => 'building_id',
                'value' => fn($model) => $model->building->name ?? null,
                'filter' => ArrayHelper::map(Building::find()->all(), 'id', 'name'),
            ],
            'floor',
            'room',
            'updated_at',
        ],
    ]); ?>
</div>
