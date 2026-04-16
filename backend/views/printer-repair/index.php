<?php
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $dataProvider yii\data\ActiveDataProvider */


$this->title = 'Ремонты принтеров';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="printer-repair-index">

    <p>
        <?= Html::a('Добавить ремонт', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'device_id',
                'value' => function($m) {
                    return Html::a($m->device->name, ['/devices/view', 'id' => $m->device_id]);
                },
                'format' => 'raw',
            ],
            'service_center',
            'started_at:date',
            [
                'label' => 'Длительность',
                'value' => function($m) {
                    $days = $m->getDurationDays();
                    if ($days === null) {
                        $started = new DateTime($m->started_at);
                        $now = new DateTime();
                        $days = $started->diff($now)->days + 1;
                        return "<span class='text-warning'>{$days} дн. (в процессе)</span>";
                    }
                    return $days . ' дн.';
                },
                'format' => 'raw',
            ],
            'cost:currency',
            [
                'attribute' => 'finished_at',
                'value' => function($m) {
                    return $m->getStatusLabel();
                },
                'format' => 'raw',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
            ],
        ],
    ]); ?>

</div>
