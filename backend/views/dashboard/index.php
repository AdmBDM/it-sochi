<?php

use yii\helpers\Html;

$this->title = 'Админка — панель управления';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="dashboard-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <ul>
        <li><?= Html::a('Организации', ['/organization']) ?></li>
        <li><?= Html::a('Подразделения', ['/department']) ?></li>
        <li><?= Html::a('Локации', ['/location']) ?></li>
        <li><?= Html::a('Здания', ['/building']) ?></li>
        <li><?= Html::a('Рабочие места', ['/workplace']) ?></li>
        <li><?= Html::a('Устройства', ['/devices']) ?></li>
        <li><?= Html::a('Сотрудники', ['/employee']) ?></li>
        <li><?= Html::a('Перемещения', ['/movement']) ?></li>
    </ul>
</div>
