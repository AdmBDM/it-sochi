<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\CartridgeType $model */

$this->title = 'Создать тип картриджа';
$this->params['breadcrumbs'][] = ['label' => 'Тип картриджа', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cartridge-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
