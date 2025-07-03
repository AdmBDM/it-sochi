<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\OrganizationBuilding $model */

$this->title = 'Update Organization Building: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Organization Buildings', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="organization-building-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
