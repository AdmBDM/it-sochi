<?php

/** @var \yii\web\View $this */
/** @var string $content */

use backend\assets\AppAsset;           // ← Добавляем импорт backend ассета
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

AppAsset::register($this);              // ← Регистрируем наш ассет (заменяет YiiAsset + BootstrapAsset + site.css)

// Опционально: CDN иконки можно оставить здесь или перенести в AppAsset
$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css');

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->title) ?></title>
<!--    <link rel="icon" type="image/svg+xml" href="/favicon.svg">-->
    <?php $this->registerCsrfMetaTags() ?>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'IT АБС-Авто Сочи',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => ['class' => 'navbar navbar-expand-md navbar-dark bg-dark'],
    ]);

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav me-auto mb-2 mb-lg-0'],
        'items' => [
            ['label' => 'Панель управления', 'url' => ['/dashboard/index']],
            ['label' => 'Организации', 'url' => ['/organization/index']],
            ['label' => 'Отделы', 'url' => ['/department/index']],
            ['label' => 'Сотрудники', 'url' => ['/employee/index']],
            ['label' => 'Техника', 'url' => ['/device/index']],
            ['label' => 'Workplace', 'url' => ['/workplace/index']],
            Yii::$app->user->isGuest
                ? ['label' => 'Войти', 'url' => ['/site/login']]
                : '<li class="nav-item">'
                . Html::beginForm(['/site/logout'], 'post', ['class' => 'form-inline'])
                . Html::submitButton(
                    'Выйти (' . Yii::$app->user->identity->username . ')',
                    ['class' => 'btn btn-link logout nav-link']
                )
                . Html::endForm()
                . '</li>'
        ],
    ]);

    NavBar::end();
    ?>

    <div class="container mt-4" style="margin-top: auto">
        <?= Breadcrumbs::widget([
            'tag' => 'nav',
            'options' => ['class' => 'breadcrumb'],
            'itemTemplate' => "<li class=\"breadcrumb-item\">{link}</li>\n",
            'activeItemTemplate' => "<li class=\"breadcrumb-item active\" aria-current=\"page\">{link}</li>\n",
            'links' => $this->params['breadcrumbs'] ?? [],
        ]) ?>

        <?= $this->render('_flashes') ?>

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
