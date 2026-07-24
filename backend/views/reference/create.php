<?php

declare(strict_types=1);

use common\models\ReferenceItem;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var common\models\ReferenceItem $model
 * @var ReferenceItem|null $parent
 */

$this->title = 'Создание элемента';
$this->params['breadcrumbs'][] = [
    'label' => 'Универсальный классификатор',
    'url' => ['index'],
];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="reference-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($parent !== null): ?>

        <div class="alert alert-secondary">
            <strong>Родитель:</strong>
            <?= Html::encode($parent->name) ?>
        </div>

    <?php endif; ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
