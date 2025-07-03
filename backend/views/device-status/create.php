<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\DeviceStatus $model */

$this->title = 'Create Device Status';
$this->params['breadcrumbs'][] = ['label' => 'Device Statuses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="device-status-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
