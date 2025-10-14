<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var common\models\Device $device */
/** @var common\models\DeviceStatus[] $statuses */
/** @var common\models\Workplace[] $workplaces */
/** @var string|null $error */

$statusList = ArrayHelper::map($statuses, 'id', 'name');
$workplaceList = ArrayHelper::map($workplaces, 'id', function($m){ return $m->name ?? ($m->id); });

?>

<div class="device-change-status-form">
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= Html::encode($error) ?></div>
    <?php endif; ?>

    <?= Html::beginForm(['device/change-status', 'id' => $device->id], 'post', ['id' => 'change-status-form']) ?>

    <?php
    foreach ($device->getDeviceDataStr(false) as $k => $v) {
        echo '<p class="dev-info"><strong>' . $k . ":</strong>\t" . $v . '</p>';
    }
    ?>

    <div class="mb-2">
        <?= Html::label('Тип изменения', null) ?><br>
        <?= Html::radioList('type_change', 'status', [
            'status' => 'Изменить статус',
            'place'  => 'Переместить (рабочее место)',
        ], ['itemOptions' => ['class' => 'form-check-input'], 'encode' => false]) ?>
    </div>

    <div id="status-block" class="mb-2">
        <?= Html::label('Новый статус', 'device_status_id') ?>
        <?= Html::dropDownList('device_status_id', null, $statusList, ['class' => 'form-control', 'prompt' => '— выбрать статус —']) ?>
    </div>

    <div id="place-block" class="mb-2" style="display:none;">
        <?= Html::label('Новое рабочее место', 'to_workplace_id') ?>
        <?= Html::dropDownList('to_workplace_id', null, $workplaceList, ['class' => 'form-control', 'prompt' => '— выбрать рабочее место —']) ?>
    </div>

    <div class="mb-2">
        <?= Html::label('Комментарий', 'comment') ?>
        <?= Html::textarea('comment', null, ['class' => 'form-control', 'rows' => 3]) ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Отмена', ['index'], ['class' => 'btn btn-secondary', 'data-dismiss' => 'modal']) ?>
    </div>

    <?= Html::endForm() ?>
</div>

<?php
$script = <<<JS
(function(){
    var \$form = $('#change-status-form');
    function toggleBlocks() {
        var t = \$form.find('input[name="type_change"]:checked').val();
        if (t === 'status') {
            $('#status-block').show(); $('#place-block').hide();
        } else {
            $('#status-block').hide(); $('#place-block').show();
        }
    }
    // initial
    toggleBlocks();
    // on change
    \$form.on('change', 'input[name="type_change"]', toggleBlocks);
})();
JS;
$this->registerJs($script);
?>
