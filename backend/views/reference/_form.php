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

function openReferenceSelector(options) {

    $('#parent-selector-modal').modal('show');

    $('#parent-selector-content').load(
        '/admin/reference/parent-selector?' +
        $.param({
            target: options.target,
            selected_id: $(options.selectedSelector).val(),
            exclude_id: $('#referenceitem-id').val(),
            rootCode: options.rootCode
        })
    );

}

$('#select-parent-button').on('click', function () {

    openReferenceSelector({
        target: 'parent_id',
        selectedSelector: '#referenceitem-parent_id',
        rootCode: null
    });
    
});

$('#select-type-button').on('click', function () {

    openReferenceSelector({
        target: 'type_id',
        selectedSelector: '#referenceitem-type_id',
        rootCode: 'type_object'
    });

});

$(document).on(
    'reference.item.selected',
    function (e, target, id, name) {

        $('#referenceitem-' + target).val(id);

        $('#referenceitem-' + target + '-name').val(name);

        $('#parent-selector-modal').modal('hide');

    }
);

JS
);

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
