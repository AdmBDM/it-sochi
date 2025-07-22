<?php

use common\models\DeviceBrand;
use common\models\DeviceModel;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\search\DeviceModelSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Device Models';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="device-model-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать Device Model', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, DeviceModel $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],

//            'id',
//            'brand_id',
            [
                'attribute' => 'brand_id',
                'label' => 'Бренд',
                'value' => function ($model) {
                    return $model->brand->name ?? null;
                },
                'filter' => ArrayHelper::map(
                    DeviceBrand::find()->orderBy('name')->all(), 'id', 'name'
                ),
            ],            'name',
//            'created_at',
            'updated_at',
        ],
    ]); ?>


</div>
