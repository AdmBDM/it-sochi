<?php
use yii\helpers\Html;

$this->title = 'Административная панель';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-index">
    <div class="jumbotron text-center">
        <h1><?= Html::encode($this->title) ?></h1>
        <p class="lead">Добро пожаловать в административную панель IT-структуры.</p>
    </div>

    <div class="body-content">
        <p>Выберите нужное действие из навигации, чтобы приступить к управлению системой.</p>
    </div>
</div>
