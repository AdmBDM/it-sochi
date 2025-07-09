<?php

use common\models\Movement;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\search\MovementSearch $searchModel */
/** @var common\models\Device $devices */
/** @var common\models\Employee $employees */
/** @var common\models\Workplace $workplaces */
/** @var common\models\Organization $organizations */
/** @var common\models\Department $departments */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Movements';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="movement-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать перемещение', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, Movement $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],

            'id',
            'device_id',
            [
                'attribute' => 'device_id',
                'value' => 'device.name',
            ],
            [
                'attribute' => 'deviceName',
                'value' => 'device.name',
                'label' => 'Устройство',
                'filter' => Html::activeTextInput($searchModel, 'deviceName', ['class' => 'form-control']),
            ],
            [
                'attribute' => 'employee_id',
                'value' => 'employee.full_name',
            ],
            'from_workplace_id',
            [
                'attribute' => 'from_workplace_id',
                'value' => 'fromWorkplace.label',
            ],
            'to_workplace_id',
            [
                'attribute' => 'to_workplace_id',
                'value' => 'toWorkplace.label',
            ],
            'moved_at',
            //'moved_by_user_id',
            //'comment:ntext',
            //'created_at',
            //'updated_at',
//            [
//                'attribute' => 'organization_id',
//                'value' => 'organization.name',
//            ],
//            [
//                'attribute' => 'department_id',
//                'value' => 'department.name',
//            ],
        ],
    ]); ?>


</div>
