<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\DeviceCartridgeType $model */

$this->title = 'Изменить Device Cartridge Type: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Device Cartridge Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменить';
?>
<div class="device-cartridge-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
