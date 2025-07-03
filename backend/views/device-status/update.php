<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\DeviceStatus $model */

$this->title = 'Update Device Status: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Device Statuses', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="device-status-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
