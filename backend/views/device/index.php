<?php

use common\models\DeviceModel;
use common\models\DeviceStatus;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\search\DeviceSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Учёт устройств';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="device-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= Html::a('Добавить устройство', ['create'], ['class' => 'btn btn-success']) ?></p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                'options' => ['style'=>'width: 90px; a:not(:last-child) {margin-right: 7px;}'],
            ],

//            'id',
            [
                'attribute' => 'name',
                'options' => ['style'=>'width: 350px;'],
            ],
            'serial_number',
            'inventory_number',
            [
                'attribute' => 'model_id',
                'value' => fn($model) => $model->model->name ?? null,
                'filter' => ArrayHelper::map(DeviceModel::find()->all(), 'id', 'name'),
            ],
            [
                'attribute' => 'status_id',
                'value' => fn($model) => $model->status->name ?? null,
                'filter' => ArrayHelper::map(DeviceStatus::find()->all(), 'id', 'name'),
            ],
            'updated_at',
        ],
    ]); ?>

</div>
