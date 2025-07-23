<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\CartridgeTransfer $model */

$this->title = 'Создать Cartridge Transfer';
$this->params['breadcrumbs'][] = ['label' => 'Cartridge Transfers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cartridge-transfer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
