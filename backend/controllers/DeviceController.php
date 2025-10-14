<?php

namespace backend\controllers;

use common\models\DeviceStatus;
use common\models\Workplace;
use Throwable;
use Yii;
use common\controllers\SochiMainController;
use common\models\Device;
use common\models\search\DeviceSearch;
use yii\db\Exception;
use yii\db\Expression;
use yii\db\StaleObjectException;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * DeviceController implements the CRUD actions for Device model.
 */
class DeviceController extends SochiMainController
{
    /**
     * @return array[]
     */
    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            // Можно добавить AccessControl при необходимости
        ];
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new DeviceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $id
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @return Response|string
     * @throws Exception
     */
    public function actionCreate(): Response|string
    {
        $model = new Device();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Устройство добавлено');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     *
     * @return Response|string
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function actionUpdate($id): Response|string
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Изменения сохранены');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     *
     * @return Response
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id): Response
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('info', 'Устройство удалено');
        return $this->redirect(['index']);
    }

    /**
     * @param $id
     *
     * @return Device
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Device
    {
        if (($model = Device::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Устройство не найдено.');
    }

    /**
     * Метод изменения статуса или рабочего места
     * AJAX / fallback: изменение статуса или рабочего места устройства
     * URL: /device/change-status?id=...
     *
     * @param $id
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionChangeStatusOld($id): string
    {
        $request = Yii::$app->request;
        $device = $this->findModel($id); // стандартный findModel() в CRUD

        // POST — сохраняем изменение и вставляем в movements
        if ($request->isPost) {
            $type = $request->post('type_change'); // 'status' или 'place'
            $comment = $request->post('comment', null);
            $userId = Yii::$app->user->id ?? null;

            // исходные значения из текущего устройства (строго то, что уже есть в модели)
            $fromWorkplace = $device->workplace_id ?? null;
            $fromStatus = $device->status_id ?? null;

            // базовый массив для вставки (всё по структуре ИТ-чата)
            $insert = [
                'device_id' => $device->id,
                'from_workplace_id' => $fromWorkplace,
                'to_workplace_id' => null,
                'from_status_id' => $fromStatus,
                'to_status_id' => null,
                'moved_at' => new Expression('NOW()'),
                'moved_by_user_id' => $userId,
                'comment' => $comment,
                'created_at' => new Expression('NOW()'),
                'updated_at' => new Expression('NOW()'),
                'device_status_id' => null,
                'is_active' => 1,
                'type_change' => $type,
                'id_old' => null,
                'id_new' => null,
            ];

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($type === 'status') {
                    $toStatus = $request->post('device_status_id') !== null ? (int)$request->post('device_status_id') : null;

                    $insert['to_status_id'] = $toStatus;
                    $insert['device_status_id'] = $toStatus;
                    $insert['id_old'] = $fromStatus;
                    $insert['id_new'] = $toStatus;

                    // обновляем устройство: только поле status_id (никаких других полей)
                    $device->status_id = $toStatus;
                    if (!$device->save(false, ['status_id'])) {
                        throw new \RuntimeException('Не удалось обновить статус устройства.');
                    }
                } elseif ($type === 'place') {
                    $toWorkplace = $request->post('to_workplace_id') !== null ? (int)$request->post('to_workplace_id') : null;

                    $insert['to_workplace_id'] = $toWorkplace;
                    $insert['id_old'] = $fromWorkplace;
                    $insert['id_new'] = $toWorkplace;

                    // обновляем устройство: только поле workplace_id
                    $device->workplace_id = $toWorkplace;
                    if (!$device->save(false, ['workplace_id'])) {
                        throw new \RuntimeException('Не удалось обновить рабочее место устройства.');
                    }
                } else {
                    throw new \RuntimeException('Неправильный тип изменения.');
                }

                // Вставляем запись в movements (поля строго как в ИТ-чате)
                Yii::$app->db->createCommand()->insert('movements', $insert)->execute();

                $transaction->commit();

                if ($request->isAjax) {
                    // как ожидает фронтенд: ровно 'success'
                    Yii::$app->response->format = Response::FORMAT_RAW;
                    return 'success';
                }

                return $this->redirect(['index']);
            } catch (\Throwable $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage(), __METHOD__);

                if ($request->isAjax) {
                    // возвращаем HTML формы с сообщением об ошибке (frontend обработает)
                    Yii::$app->response->format = Response::FORMAT_HTML;
                    return $this->renderAjax('_change_status_form', [
                        'device' => $device,
                        'statuses' => DeviceStatus::find()->orderBy('name')->all(),
                        'workplaces' => Workplace::find()->orderBy('name')->all(),
                        'error' => $e->getMessage(),
                    ]);
                }

                Yii::$app->session->setFlash('error', 'Ошибка: ' . $e->getMessage());
                return $this->redirect(['index']);
            }
        }

        // GET — отдать форму (AJAX -> renderAjax, иначе обычный рендер)
        $params = [
            'device' => $device,
            'statuses' => DeviceStatus::find()->orderBy('name')->all(),
            'workplaces' => Workplace::find()->orderBy('name')->all(),
        ];

        if ($request->isAjax) {
            return $this->renderAjax('_change_status_form', $params);
        }

        return $this->render('change-status', $params);
    }

    public function actionChangeStatus($id): string
    {
        $request = Yii::$app->request;
        $device = $this->findModel($id); // стандартный findModel() в CRUD

        if ($request->isPost) {
            $type = $request->post('type_change'); // 'status' или 'place'
            $comment = $request->post('comment', null);
            $userId = Yii::$app->user->id ?? null;

            // исходные значения из текущего устройства
            $fromWorkplace = $device->workplace_id ?? null;
            $fromStatus = $device->status_id ?? null;

            // базовый массив для вставки (всё по структуре ИТ-чата)
            $insert = [
                'device_id' => $device->id,
                'from_workplace_id' => $fromWorkplace,
                'to_workplace_id' => $toWorkplace ?? $fromWorkplace,
                'moved_at' => new Expression('NOW()'),
                'moved_by_user_id' => $userId,
                'comment' => $comment,
                'created_at' => new Expression('NOW()'),
                'updated_at' => new Expression('NOW()'),
                'device_status_id' => null,
                'is_active' => true,
                'type_change' => $type,
                'id_old' => null,
                'id_new' => null,

            ];
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($type === 'status') {
                    $toStatus = $request->post('device_status_id') !== null
                        ? (int)$request->post('device_status_id')
                        : null;

                    $insert['device_status_id'] = $toStatus;
                    $insert['id_old'] = $fromStatus;
                    $insert['id_new'] = $toStatus;

                    // обновляем устройство
                    $device->status_id = $toStatus;
                    if (!$device->save(false, ['status_id'])) {
                        throw new \RuntimeException('Не удалось обновить статус устройства.');
                    }
                } elseif ($type === 'place') {
                    $toWorkplace = $request->post('to_workplace_id') !== null
                        ? (int)$request->post('to_workplace_id')
                        : null;

                    $insert['to_workplace_id'] = $toWorkplace;
                    $insert['id_old'] = $fromWorkplace;
                    $insert['id_new'] = $toWorkplace;

                    // обновляем устройство
                    $device->workplace_id = $toWorkplace;
                    if (!$device->save(false, ['workplace_id'])) {
                        throw new \RuntimeException('Не удалось обновить рабочее место устройства.');
                    }
                } else {
                    throw new \RuntimeException('Неправильный тип изменения.');
                }

                // вставка в movements
                Yii::$app->db->createCommand()->insert('movements', $insert)->execute();

                $transaction->commit();

                if ($request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_RAW;
                    return 'success';
                }

                return $this->redirect(['index']);
            } catch (\Throwable $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage(), __METHOD__);

                if ($request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_HTML;
                    return $this->renderAjax('_change_status_form', [
                        'device' => $device,
                        'statuses' => DeviceStatus::find()->orderBy('name')->all(),
                        'workplaces' => Workplace::find()->orderBy('name')->all(),
                        'error' => $e->getMessage(),
                    ]);
                }

                Yii::$app->session->setFlash('error', 'Ошибка: ' . $e->getMessage());
                return $this->redirect(['index']);
            }
        }

        // GET — отдать форму (AJAX -> renderAjax, иначе обычный рендер)
        $params = [
            'device' => $device,
            'statuses' => DeviceStatus::find()->orderBy('name')->all(),
            'workplaces' => Workplace::find()->orderBy('name')->all(),
        ];

        if ($request->isAjax) {
            return $this->renderAjax('_change_status_form', $params);
        }

        return $this->render('change-status', $params);
    }


}
