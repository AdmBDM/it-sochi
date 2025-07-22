<?php

use common\models\DeviceBrand;
use common\models\DeviceModel;
use common\models\DeviceStatus;
use common\models\DeviceType;
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
//            [
//                'attribute' => 'name',
//                'options' => ['style'=>'width: 350px;'],
//            ],
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
                'attribute' => 'status_id',
                'value' => fn($model) => $model->status->name ?? null,
                'filter' => ArrayHelper::map(DeviceStatus::find()->all(), 'id', 'name'),
            ],
//            'updated_at',
        ],
    ]); ?>

</div>
