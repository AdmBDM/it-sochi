<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Movement $model */
/** @var common\models\Device $devices */
/** @var common\models\Employee $employees */
/** @var common\models\Workplace $workplaces */
/** @var common\models\Organization $organizations */
/** @var common\models\Department $departments */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Movements', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="movement-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'device_id',
            [
                'attribute' => 'device_id',
                'value' => $model->device->name ?? '(не указано)',
            ],
            [
                'attribute' => 'employee_id',
                'value' => $model->employee->fullname ?? '(не указано)',
            ],
            'from_workplace_id',
            [
                'attribute' => 'from_workplace_id',
                'value' => $model->fromWorkplace->label ?? '(не указано)',
            ],
            'to_workplace_id',
            [
                'attribute' => 'to_workplace_id',
                'value' => $model->toWorkplace->label ?? '(не указано)',
            ],
            'moved_at',
            'moved_at:datetime',
            'moved_by_user_id',
            'comment:ntext',
            'created_at',
            'created_at:datetime',
            'updated_at',
            'updated_at:datetime',
//            [
//                'attribute' => 'organization_id',
//                'value' => $model->organization->name ?? '(не указано)',
//            ],
//            [
//                'attribute' => 'department_id',
//                'value' => $model->department->name ?? '(не указано)',
//            ],
        ],
    ]) ?>

</div>
