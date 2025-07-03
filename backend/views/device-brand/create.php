<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\DeviceBrand $model */

$this->title = 'Create Device Brand';
$this->params['breadcrumbs'][] = ['label' => 'Device Brands', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="device-brand-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
