<?php

declare(strict_types=1);

use common\models\ReferenceItem;
use yii\bootstrap5\Modal;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var ReferenceItem $model
 * @var array<int,string> $parentList
 * @var $typeList
 */

$form = ActiveForm::begin(['id' => 'reference-form',]);

Modal::begin([
        'id' => 'parent-selector-modal',
        'title' => 'Выбор родителя',
        'size' => Modal::SIZE_LARGE,
]);
echo '<div id="parent-selector-content" class="bg-light p-3"></div>';
Modal::end();

?>

<div class="card">

    <div class="card-body">
        <?= $form->field($model, 'parent_id')->hiddenInput()->label(false) ?>

        <div class="mb-3">
            <?= Html::label(
                    'Родительский элемент',
                    'referenceitem-parent_id-name',
                    [
                            'class' => 'form-label',
                    ]
            ) ?>

            <div class="input-group">
                <?= Html::textInput(
                        'parent_name',
                        $model->parent?->name,
                        [
                                'id' => 'referenceitem-parent_id-name',
                                'class' => 'form-control',
                                'readonly' => true,
                                'placeholder' => 'Корневой элемент',
                        ]
                ) ?>
                <button
                        type="button"
                        id="select-parent-button"
                        class="btn btn-outline-secondary">
                    Выбрать
                </button>
            </div>
        </div>

        <?= $form->field($model, 'name')->textInput([
                'maxlength' => true,
                'autocomplete' => 'off',
                'id' => 'referenceitem-name',
        ]) ?>

        <?= $form->field($model, 'code')->textInput([
                'maxlength' => true,
                'autocomplete' => 'off',
                'id' => 'referenceitem-code',
        ]) ?>

        <?= $form->field($model, 'type_id')->hiddenInput()->label(false) ?>

        <div class="mb-3">
            <?= Html::label(
                    'Тип',
                    'referenceitem-type_id-name',
                    [
                            'class' => 'form-label',
                    ]
            ) ?>

            <div class="input-group">
                <?= Html::textInput(
                        'type_name',
                        $model->type?->name,
                        [
                                'id' => 'referenceitem-type_id-name',
                                'class' => 'form-control',
                                'readonly' => true,
                                'placeholder' => 'Не указан',
                        ]
                ) ?>

                <button
                        type="button"
                        id="select-type-button"
                        class="btn btn-outline-secondary">
                    Выбрать
                </button>
            </div>
        </div>

        <?= $form->field($model, 'description')->textarea(['rows' => 4,]) ?>
<!--        --><?php //= $form->field($model, 'sort_order')->input('number') ?>
        <?= $form->field($model, 'is_active')->checkbox() ?>

    </div>

    <div class="card-footer">

        <?= Html::submitButton(
            'Сохранить',
            [
                'class' => 'btn btn-success',
            ]
        ) ?>

        <?= Html::a(
            'Отмена',
            ['index'],
            [
                'class' => 'btn btn-secondary',
            ]
        ) ?>

    </div>

</div>

<?php ActiveForm::end(); ?>
