<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Building $model */

$this->title = 'Создать Building';
$this->params['breadcrumbs'][] = ['label' => 'Buildings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="building-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
