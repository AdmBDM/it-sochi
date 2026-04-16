<?php
// backend/views/printer-repair/update.php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\PrinterRepair $model */
/** @var common\models\Device[] $printers */

$this->title = 'Изменение ремонта №' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Ремонты принтеров', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Ремонт №' . $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменение';
?>

<div class="printer-repair-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'printers' => $printers,
    ]) ?>

</div>
