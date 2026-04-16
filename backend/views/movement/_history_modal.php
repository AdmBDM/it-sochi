<?php

use yii\grid\GridView;
use yii\data\ArrayDataProvider;

/**
 * @var \common\models\Movement[] $movements
 */

$dataProvider = new ArrayDataProvider([
    'allModels' => $movements,
    'pagination' => [
        'pageSize' => 10,
    ],
]);

?>

<div class="p-3">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'moved_at',
                'format' => ['datetime', 'php:d.m.Y H:i'],
                'label' => 'Дата перемещения',
            ],
            [
                'attribute' => 'type_change',
                'label' => 'Тип изменения',
                'value' => fn($model) => $model->type_change === 'place' ? 'Перемещение' : 'Изменение статуса',
            ],
            [
                'attribute' => 'oldValue',
                'label' => 'Старое значение',
                'value' => fn($model) => $model->oldValue,
            ],
            [
                'attribute' => 'newValue',
                'label' => 'Новое значение',
                'value' => fn($model) => $model->newValue,
            ],
            [
                'attribute' => 'employee',
                'label' => 'Сотрудник',
                'value' => fn($model) => $model->employee ? $model->employee->famIO : null,
            ],
            [
                'attribute' => 'comment',
                'label' => 'Комментарий',
                'contentOptions' => ['style' => 'max-width:300px; white-space:normal;'],
            ],
        ],
    ]) ?>
</div>
