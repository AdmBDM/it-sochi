<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\DeviceBrand $model */

$this->title = 'Update Device Brand: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Device Brands', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="device-brand-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
