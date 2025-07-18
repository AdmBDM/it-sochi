<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Workplace $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Рабочие места', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="workplace-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => 'Удалить это место?', 'method' => 'post'],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            [
                'label' => 'Сотрудник',
                'value' => $model->employee->lastname . ' ' . $model->employee->firstname ?? null,
            ],
            [
                'label' => 'Подразделение',
                'value' => $model->department->name ?? null,
            ],
            [
                'label' => 'Локация',
                'value' => $model->location->name ?? null,
            ],
            'comment:ntext',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
