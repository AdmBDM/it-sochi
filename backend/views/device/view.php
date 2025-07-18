<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Device $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Устройства', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="device-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => 'Удалить это устройство?', 'method' => 'post'],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'serial_number',
            'inventory_number',
            [
                'label' => 'Модель',
                'value' => $model->model->name,
            ],
            [
                'label' => 'Бренд',
                'value' => $model->brand->name ?? null,
            ],
            [
                'label' => 'Тип',
                'value' => $model->type->name ?? null,
            ],
            [
                'label' => 'Рабочее место',
                'value' => $model->workplace->name ?? null,
            ],
            [
                'label' => 'Сотрудник',
                'value' => $model->employee->fullName ?? null,
            ],
            'comment:ntext',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
