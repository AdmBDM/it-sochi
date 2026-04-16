<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $model common\models\PrinterRepair */
?>

<div class="printer-repair-view">

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Удалить запись?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'device_id',
                'value' => $model->device->name . ' (' . $model->device->model->fullName . ')',
            ],
            'service_center',
            'description:ntext',
            [
                'label' => 'Запчасти',
                'value' => $model->parts_replaced ? implode(', ', json_decode($model->parts_replaced, true)) : '—',
                'format' => 'raw',
            ],
            'cost:currency',
            'started_at:date',
            'finished_at:date',
            [
                'label' => 'Дней в ремонте',
                'value' => $model->getDurationDays() ?? '—',
            ],
            'document_number',
            'created_at:datetime',
        ],
    ]) ?>

</div>
