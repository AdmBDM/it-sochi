<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\OrganizationBuilding $model */

$this->title = 'Создать Organization Building';
$this->params['breadcrumbs'][] = ['label' => 'Organization Buildings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="organization-building-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
