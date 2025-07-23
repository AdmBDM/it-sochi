<?php

use common\models\CartridgeTransfer;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\search\CartridgeTransferSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Cartridge Transfers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cartridge-transfer-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать Cartridge Transfer', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'type',
            'code',
            'created_at',
            'updated_at',
            //'is_active:boolean',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, CartridgeTransfer $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
