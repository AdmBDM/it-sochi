<?php

use common\models\DeviceCartridgeType;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\search\DeviceCartridgeTypeSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Device Cartridge Types';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="device-cartridge-type-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать Device Cartridge Type', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'device_id',
            'cartridge_type_id',
            'created_at',
            'updated_at',
            //'is_active:boolean',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, DeviceCartridgeType $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
