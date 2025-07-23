<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\DeviceCartridgeType $model */

$this->title = 'Создать Device Cartridge Type';
$this->params['breadcrumbs'][] = ['label' => 'Device Cartridge Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="device-cartridge-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
