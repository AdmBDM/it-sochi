<?php

use yii\helpers\Html;

$this->title = 'Админка — панель управления';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="dashboard-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <ul>
        <h3>Основные таблицы</h3>
        <li><?= Html::a('Локации', ['/location']) ?></li>
        <li><?= Html::a('Рабочие места', ['/workplace']) ?></li>
        <li><?= Html::a('Устройства', ['/device']) ?></li>
        <li><?= Html::a('Перемещения', ['/movement']) ?></li>

        <hr>
        <h3>Картриджи</h3>
        <li><?= Html::a('Типы картриджей', ['/cartridge-type']) ?></li>
        <li><?= Html::a('Принтеры - картриджи', ['/device-cartridge-type']) ?></li>
        <li><?= Html::a('Движение картриджей', ['/cartridge-transfer']) ?></li>

        <hr>
        <h3>Справочники</h3>
        <li><?= Html::a('Сотрудники', ['/employee']) ?></li>
        <li><?= Html::a('Здания', ['/building']) ?></li>
        <li><?= Html::a('Организации', ['/organization']) ?></li>
        <li><?= Html::a('Подразделения', ['/department']) ?></li>
        <li><?= Html::a('Типы оборудования', ['/device-type']) ?></li>
        <li><?= Html::a('Бренды', ['/device-brand']) ?></li>
        <li><?= Html::a('Модели', ['/device-model']) ?></li>

    </ul>
</div>
