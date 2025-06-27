<?php

/** @var View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\web\View;

Yii::$app->name = Yii::$app->params['app_title'];
$this->title = Yii::$app->name;
AppAsset::register($this);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <script src="https://use.fontawesome.com/fadba6afe8.js"></script>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header>
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar navbar-expand-md navbar-dark bg-dark fixed-top',
        ],
    ]);
    $menuItems = [];
	foreach (Yii::$app->params['blocks'] as $k => $v) {
		if ($v['on'] & $v['nav_menu']) {
			$menuItems = array_merge($menuItems,
				[['label' => $v['title_short'], 'url' => ['#' . Yii::$app->params['prefix_blocks_id'] . $k]]]);
		}
	}
	$menuItems = array_merge($menuItems,
			[['label' => 'Админка', 'url' => ['/admin']]]);

    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
    } else {
        $menuItems[] = '<li>'
            . Html::beginForm(['/site/logout'], 'post', ['class' => 'form-inline'])
            . Html::submitButton(
                'Logout (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav ml-auto'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>
</header>

<main role="main" class="flex-shrink-0">
	<div class="container">
        <?= Breadcrumbs::widget([
            'links' => $this->params['breadcrumbs'] ?? [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer class="footer mt-auto py-3 text-muted">
	<div class="col-md-12 col-sm-12">
		<div class="container">
			<p class="float-left">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></p>
		</div>
	</div>
</footer>

<?php $this->endBody() ?>
<script>
	$('body').append('<div class="btn-up"></div>');
	$(window).scroll(function() {
		if ($(this).scrollTop() > 100) {
			$('.btn-up').css({
				transform: 'scale(1)'
			});
		} else {
			$('.btn-up').css({
				transform: 'scale(0)'
			});
		}
	});
	$('.btn-up').on('click',function() {
		$('html, body').animate({
			scrollTop: 0
		}, 500);
		return false;
	});
</script>
</body>
</html>
<?php $this->endPage();
