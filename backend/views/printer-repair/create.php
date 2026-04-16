<?php

// backend/views/printer-repair/create.php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\PrinterRepair $model */
/** @var common\models\Device[] $printers */

$this->title = 'Добавление ремонта';
$this->params['breadcrumbs'][] = ['label' => 'Ремонты принтеров', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="printer-repair-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'printers' => $printers,
    ]) ?>

</div>
