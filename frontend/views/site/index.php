<?php
use yii\helpers\Html;

$this->title = 'Главная страница портала';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-index">
    <div class="jumbotron text-center">
        <h1><?= Html::encode($this->title) ?></h1>
        <p class="lead">Добро пожаловать на портал IT-структуры.</p>
    </div>

    <div class="body-content">
        <p>Это стартовая страница фронтенда. Здесь будет размещена общая информация.</p>
    </div>
</div>
