<?php

/** @var \yii\web\View $this */
/** @var string $content */

use yii\helpers\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\widgets\Breadcrumbs;
use yii\web\YiiAsset;
use yii\bootstrap5\BootstrapAsset;

YiiAsset::register($this);
BootstrapAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'IT Sochi — ' . (Yii::$app->controller->module->id ?? 'frontend'),
        'brandUrl' => Yii::$app->homeUrl,
        'options' => ['class' => 'navbar navbar-expand-md navbar-dark bg-dark'],
    ]);

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav ms-auto'],
        'items' => Yii::$app->user->isGuest
            ? [
                ['label' => 'Вход', 'url' => ['/site/login']],
            ]
            : [
                '<li class="nav-item">'
                . Html::beginForm(['/site/logout'], 'post', ['class' => 'd-inline'])
                . Html::submitButton(
                    'Выход (' . Html::encode(Yii::$app->user->identity->username) . ')',
                    ['class' => 'btn btn-link nav-link logout', 'style' => 'padding: 0']
                )
                . Html::endForm()
                . '</li>',
            ],
    ]);

    NavBar::end();
    ?>

    <div class="container mt-4">
        <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs'] ?? []]) ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer text-muted text-center mt-5">
    <div class="container">
        <p>© IT Sochi <?= date('Y') ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
