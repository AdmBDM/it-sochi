<?php

use yii\widgets\DetailView;

/**
 * @var $model common\models\Movement
 */

?>

<div class="p-3">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'label' => 'Устройство',
                'value' => $model->device ? $model->device->fullName : null,
            ],
            [
                'label' => 'Тип изменения',
                'value' => $model->type_change === 'place' ? 'Перемещение' : 'Изменение статуса',
            ],
            [
                'label' => 'Старое значение',
                'value' => $model->oldValue,
            ],
            [
                'label' => 'Новое значение',
                'value' => $model->newValue,
            ],
            [
                'label' => 'Сотрудник',
                'value' => $model->employee ? $model->employee->famIO : null,
            ],
            'moved_at:datetime',
            [
                'attribute' => 'comment',
                'format' => 'ntext',
                'label' => 'Комментарий',
            ],
        ],
    ]) ?>
</div>
