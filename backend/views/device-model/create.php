<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\DeviceModel $model */

$this->title = 'Create Device Model';
$this->params['breadcrumbs'][] = ['label' => 'Device Models', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="device-model-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
