<?php

namespace backend\modules\trassir\controllers;

use backend\modules\trassir\models\EventSearch;
use backend\modules\trassir\models\PacsEventSearch;
use common\controllers\SochiMainController;
use common\models\trassir\EventLog;
use common\models\trassir\PacsEvent;
use Throwable;
use Yii;

class EventController extends SochiMainController
{
    public function actionIndex(): string
    {
        $searchModel = new EventSearch();

        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams
        );

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionEvents()
    {
        $request = Yii::$app->request;

        $dateFrom = $request->post('from');
        $dateTo   = $request->post('to');
        $ts = (int)$request->post('ts');

        $from = null;
        $to = null;

        if ($dateFrom !== null && $dateFrom !== '') {
            $from = strtotime($dateFrom . ' 00:00:00') * 1000000;
        }

        if ($dateTo !== null && $dateTo !== '') {
            $to = strtotime($dateTo . ' 23:59:59') * 1000000;
        }

        $searchModel = new PacsEventSearch();

        $dataProvider = $searchModel->search($from, $to, $ts);

        return $this->renderAjax('_events_grid', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdateTime(): string
    {
        $request = Yii::$app->request;

        if ($request->post('save')) {

            $newEventTs = strtotime($request->post('eventTs')) * 1000000;
            $newOffsetTs = strtotime($request->post('offsetTs')) * 1000000;

            $db = PacsEvent::getDb();
            $transaction = $db->beginTransaction();

            try {

                $oldEventTs = (int)$request->post('oldEventTs');

                $count = PacsEvent::updateAll(
                    [
                        'event_ts' => $newEventTs,
                        'event_ts_with_device_offset' => $newOffsetTs,
                    ],
                    [
                        'event_ts' => $oldEventTs,
                    ]
                );

                EventLog::updateAll(
                    [
                        'ts' => $newEventTs,
                    ],
                    [
                        'ts' => $oldEventTs,
                    ]
                );

                $transaction->commit();

                return json_encode([
                    'success' => true,
                    'updated' => $count,
                ]);

            } catch (Throwable $e) {

                $transaction->rollBack();

                throw $e;
            }
        }

        return $this->renderAjax('_update_time', [
            'eventTs' => (int)$request->post('eventTs'),
            'offsetTs' => (int)$request->post('offsetTs'),
        ]);
    }

}
