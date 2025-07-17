<?php

namespace frontend\controllers;

use common\controllers\SochiMainController;
use Yii;
use common\models\DisplayConfig;
use yii\web\NotFoundHttpException;

class InfoAdminController extends SochiMainController
{
    public function actionIndex()
    {
        $configs = DisplayConfig::find()->all();
        return $this->render('index', ['configs' => $configs]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', ['model' => $model]);
    }

    protected function findModel($id)
    {
        if (($model = DisplayConfig::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Конфигурация не найдена.');
    }
}
