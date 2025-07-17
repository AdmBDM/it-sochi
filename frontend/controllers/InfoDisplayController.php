<?php
namespace frontend\controllers;

use yii\web\Controller;
use common\models\WorkerSchedule;

class InfoDisplayController extends Controller
{
    public $layout = false; // можно отдельный layout для экранов

    public function actionIndex($id = null)
    {
        return $this->render('index', ['id' => $id]);
    }

    public function actionData($id = null)
    {
        $data = WorkerSchedule::find()
            ->orderBy(['worker_name' => SORT_ASC])
            ->asArray()
            ->all();

        return $this->asJson([
            'screen_id' => $id,
            'blocks' => [
                [
                    'type' => 'schedule',
                    'data' => $data,
                    'duration' => 60
                ]
            ]
        ]);
    }
}
