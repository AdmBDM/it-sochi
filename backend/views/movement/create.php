<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Movement $model */

$this->title = 'Создать Movement';
$this->params['breadcrumbs'][] = ['label' => 'Movements', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="movement-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
