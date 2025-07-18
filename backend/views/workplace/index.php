<?php

use common\models\Department;
use common\models\Employee;
use common\models\Location;
use common\models\Workplace;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\search\WorkplaceSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Рабочие места';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="workplace-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= Html::a('Добавить место', ['create'], ['class' => 'btn btn-success']) ?></p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
            ['class' => 'yii\grid\ActionColumn'],
            'id',
            'name',
            [
                'attribute' => 'employee_id',
//                'value' => fn($model) => $model->employee->lastname . ' ' . $model->employee->firstname ?? null,
                'value' => fn($model) => $model->employee->full_name ?? null,
//                'filter' => ArrayHelper::map(Employee::find()->all(), 'id', fn($e) => $e->lastname . ' ' . $e->firstname),
                'filter' => ArrayHelper::map(Employee::find()->all(), 'id', fn($e) => $e->full_name),
            ],
            [
                'attribute' => 'department_id',
                'value' => fn($model) => $model->department->name ?? null,
                'filter' => ArrayHelper::map(Department::find()->all(), 'id', 'name'),
            ],
            [
                'attribute' => 'location_id',
                'value' => fn($model) => $model->location->name ?? null,
                'filter' => ArrayHelper::map(Location::find()->all(), 'id', 'name'),
            ],
            'updated_at',
        ],
    ]); ?>

</div>
