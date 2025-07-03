<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\DeviceType $model */

$this->title = 'Create Device Type';
$this->params['breadcrumbs'][] = ['label' => 'Device Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="device-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
