<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Movement $model */
/** @var yii\widgets\ActiveForm $form */
/** @var array $devices */
/** @var array $employees */
/** @var array $workplaces */

$devices = Yii::$app->view->params['devices'];
$employees = Yii::$app->view->params['employees'];
$workplaces = Yii::$app->view->params['workplaces'];

?>

<div class="movement-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'device_id')->dropDownList($devices, ['prompt' => 'Выберите устройство']) ?>

    <?= $form->field($model, 'from_workplace_id')->dropDownList($workplaces, ['prompt' => 'Выберите рабочее место откуда']) ?>

    <?= $form->field($model, 'to_workplace_id')->dropDownList($workplaces, ['prompt' => 'Выберите рабочее место куда']) ?>

    <?= $form->field($model, 'moved_by_user_id')->dropDownList($employees, ['prompt' => 'Ответственный сотрудник']) ?>

    <?= $form->field($model, 'moved_at')->input('datetime-local', [
        'value' => $model->moved_at ? date('Y-m-d\TH:i', strtotime($model->moved_at)) : date('Y-m-d\TH:i'),
    ]) ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 3]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
