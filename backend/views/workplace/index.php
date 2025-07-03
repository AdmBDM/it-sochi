<?php

use common\models\Workplace;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\search\WorkplaceSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Workplaces';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="workplace-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Workplace', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, Workplace $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],

            'id',
            'employee_id',
            [
                'attribute' => 'employee_id',
                'value' => fn($model) => $model->employee->fullName ?? null,
                'filter' => ArrayHelper::map(\common\models\Employee::find()->all(), 'id', 'fullName'),
                'label' => 'Сотрудник',
            ],
            'location_id',
            [
                'attribute' => 'location_id',
                'value' => fn($model) => $model->location->name ?? null,
                'filter' => ArrayHelper::map(\common\models\Location::find()->all(), 'id', 'name'),
                'label' => 'Локация',
            ],
            'department_id',
            [
                'attribute' => 'department_id',
                'value' => fn($model) => $model->department->name ?? null,
                'filter' => ArrayHelper::map(\common\models\Department::find()->all(), 'id', 'name'),
                'label' => 'Здание',
            ],
            'comment:ntext',
            //'created_at',
            //'updated_at',

        ],
    ]); ?>


</div>
