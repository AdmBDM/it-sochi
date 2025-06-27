<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var yii\base\Model $model */
/** @var yii\bootstrap5\ActiveForm $form */

$this->title = 'Вход';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>
    <p class="text-muted">Введите логин и пароль для входа:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

            <?= $form->field($model, 'username')
                ->textInput(['autofocus' => true])
                ->label('Имя пользователя') ?>

            <?= $form->field($model, 'password')
                ->passwordInput()
                ->label('Пароль') ?>

            <?= $form->field($model, 'rememberMe')->checkbox()
                ->label('Запомнить меня') ?>

            <div class="form-group mt-3">
                <?= Html::submitButton('Войти', ['class' => 'btn btn-primary w-100', 'name' => 'login-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
