<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Workplace $model */

$this->title = 'Создать рабочее место';
$this->params['breadcrumbs'][] = ['label' => 'Рабочие места', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="workplace-create">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', ['model' => $model]) ?>

</div>
