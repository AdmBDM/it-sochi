<?php

declare(strict_types=1);

use common\models\ReferenceItem;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var ReferenceItem $model
 * @var array<int,string> $parentList
 * @var $typeList
 */

$form = ActiveForm::begin(['id' => 'reference-form',]);
?>

<div class="card">

    <div class="card-body">
        <?= $form->field($model, 'parent_id')->dropDownList(
                $parentList,
                [
                        'prompt' => 'Корневой элемент',
                ]
        ) ?>

        <?= $form->field($model, 'name')->textInput([
                'maxlength' => true,
                'autocomplete' => 'off',
        ]) ?>

        <?= $form->field($model, 'code')->textInput([
                'maxlength' => true,
                'autocomplete' => 'off',
        ]) ?>

        <?= $form->field($model, 'type_id')->dropDownList($typeList, ['prompt' => 'Не указан',]) ?>
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
