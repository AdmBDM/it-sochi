<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\DeviceModel $model */

$this->title = 'Изменить Device Model: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Device Models', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменить';
?>
<div class="device-model-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
