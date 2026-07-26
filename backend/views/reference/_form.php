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

$this->registerJs(<<<JS

(function () {

    let codeChanged = false;

    const nameField = $('#referenceitem-name');
    const codeField = $('#referenceitem-code');

    codeField.on('input', function () {
        codeChanged = true;
    });

    function transliterate(text) {

        const map = {
            'а':'a','б':'b','в':'v','г':'g','д':'d',
            'е':'e','ё':'e','ж':'zh','з':'z','и':'i',
            'й':'y','к':'k','л':'l','м':'m','н':'n',
            'о':'o','п':'p','р':'r','с':'s','т':'t',
            'у':'u','ф':'f','х':'h','ц':'c','ч':'ch',
            'ш':'sh','щ':'sch','ъ':'','ы':'y','ь':'',
            'э':'e','ю':'yu','я':'ya'
        };

        return text
            .toLowerCase()
            .split('')
            .map(function (char) {
                return map[char] ?? char;
            })
            .join('')
            .replace(/[^a-z0-9]+/g, '_')
            .replace(/_+/g, '_')
            .replace(/^_+|_+$/g, '');
    }

    nameField.on('input', function () {

        if (codeChanged) {
            return;
        }

        codeField.val(
            transliterate($(this).val())
        );

    });

})();

JS
);


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
                'id' => 'referenceitem-name',
        ]) ?>

        <?= $form->field($model, 'code')->textInput([
                'maxlength' => true,
                'autocomplete' => 'off',
                'id' => 'referenceitem-code',
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
