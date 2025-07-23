<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\CartridgeTransfer $model */

$this->title = 'Изменить Cartridge Transfer: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Cartridge Transfers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменить';
?>
<div class="cartridge-transfer-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
