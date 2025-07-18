<?php

/** @var \yii\web\View $this */
/** @var string $content */

use yii\bootstrap5\BootstrapAsset;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\Breadcrumbs;

YiiAsset::register($this);
BootstrapAsset::register($this);
$this->registerCssFile('@web/css/site.css', [
    'depends' => [YiiAsset::class],
]);

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
