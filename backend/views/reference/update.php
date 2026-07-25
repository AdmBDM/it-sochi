<?php

declare(strict_types=1);

use common\models\ReferenceItem;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var ReferenceItem $model
 * @var ReferenceItem|null $parent
 * @var array<int,string> $parentList
 */

$this->title = 'Редактирование элемента';

$this->params['breadcrumbs'][] = [
    'label' => 'Универсальный классификатор',
    'url' => ['index'],
];

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="reference-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($parent !== null): ?>
        <div class="alert alert-secondary">
            <strong>Родитель:</strong>
            <?= Html::encode($parent->name) ?>
        </div>
    <?php endif; ?>

    <?= $this->render('_form', [
        'model' => $model,
        'parentList' => $parentList,
    ]) ?>

</div>
