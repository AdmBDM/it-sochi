<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\CartridgeType $model */

$this->title = 'Изменить тип картриджа: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Тип картриджа', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменить';
?>
<div class="cartridge-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
