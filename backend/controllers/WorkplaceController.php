<?php

namespace backend\controllers;

use Throwable;
use Yii;
use common\controllers\SochiMainController;
use common\models\Building;
use common\models\Location;
use common\models\Workplace;
use common\models\search\WorkplaceSearch;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * WorkplaceController implements the CRUD actions for Workplace model.
 */
class WorkplaceController extends SochiMainController
{
    /**
     * @return array[]
     */
    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['delete' => ['POST']],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new WorkplaceSearch();
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
        $model = new Workplace();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Рабочее место создано');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', ['model' => $model]);
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

        return $this->render('update', ['model' => $model]);
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
        Yii::$app->session->setFlash('info', 'Рабочее место удалено');
        return $this->redirect(['index']);
    }

    /**
     * @param $id
     *
     * @return Workplace
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Workplace
    {
        if (($model = Workplace::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Рабочее место не найдено.');
    }

    /**
     * @return array
     */
    public function actionLocationList(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $departmentId = Yii::$app->request->post('department_id');
        if (!$departmentId) {
            return [];
        }

        $locations = Location::find()
            ->where(['in', 'id', Workplace::find()
                ->select('location_id')
                ->where(['department_id' => $departmentId])
                ->andWhere(['is not', 'location_id', null])
            ])
            ->with('building')
            ->orderBy(['floor' => SORT_ASC])
            ->all();

        return array_map(function($loc) {
            return [
                'id' => $loc->id,
                'name' => $loc->building->name . ' — эт.' . $loc->floor . ($loc->room ? ' — ' . $loc->room : '')
            ];
        }, $locations);
    }

    /**
     * Возвращает список помещений (room)
     * для выбранного здания и подразделения
     * и в которых созданы рабочие места
     *
     * @return array
     */
    public function actionRoomList(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $buildingId = Yii::$app->request->post('building_id');
        $departmentId = Yii::$app->request->post('department_id');

        if (!$buildingId || !$departmentId) {
            return [];
        }

        $rooms = Location::find()
            ->alias('l')
            ->innerJoin(
                Workplace::tableName() . ' w',
                'w.location_id = l.id'
            )
            ->where([
                'l.building_id' => $buildingId,
//                'w.department_id' => $departmentId,
                'l.is_active' => true,
            ])
            ->andWhere(['is not', 'l.room', null])
            ->select(['l.room'])
            ->distinct()
            ->orderBy([
                'l.room' => SORT_ASC,
            ])
            ->column();

        return array_map(
            fn($room) => [
                'id' => $room,
                'name' => $room,
            ],
            $rooms
        );
    }

    /**
     * Возвращает этажи выбранного помещения,
     * где созданы рабочие места
     *
     * Одновременно возвращается location_id.
     *
     * @return array
     */
    public function actionFloorList(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $buildingId = Yii::$app->request->post('building_id');
//        $departmentId = Yii::$app->request->post('department_id');
        $room = Yii::$app->request->post('room');

//        if (!$buildingId || !$departmentId || !$room) {
        if (!$buildingId || !$room) {
            return [];
        }

        $locations = Location::find()
            ->alias('l')
            ->innerJoin(
                Workplace::tableName() . ' w',
                'w.location_id = l.id'
            )
            ->where([
                'l.building_id' => $buildingId,
//                'w.department_id' => $departmentId,
                'l.room' => $room,
                'l.is_active' => true,
            ])
            ->select([
                'l.id',
                'l.floor',
            ])
            ->distinct()
            ->orderBy([
                'l.floor' => SORT_ASC,
            ])
            ->asArray()
            ->all();

        return array_map(
            static fn(array $item) => [
                'id' => (int)$item['id'],
                'name' => $item['floor'],
            ],
            $locations
        );
    }

    /**
     * Возвращает список помещений выбранного здания.
     * Используется при создании/редактировании рабочего места.
     *
     * Источник данных — таблица locations.
     *
     * @return array
     */
    public function actionLocationRoomList(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $buildingId = Yii::$app->request->post('building_id');

        if (!$buildingId) {
            return [];
        }

        $rooms = Location::find()
            ->where([
                'building_id' => $buildingId,
                'is_active'   => true,
            ])
            ->andWhere(['is not', 'room', null])
            ->select('room')
            ->distinct()
            ->orderBy([
                'room' => SORT_ASC,
            ])
            ->column();

        return array_map(
            static fn(string $room) => [
                'id'   => $room,
                'name' => $room,
            ],
            $rooms
        );
    }

    /**
     * Возвращает список этажей выбранного помещения.
     * Используется при создании/редактировании рабочего места.
     *
     * Источник данных — таблица locations.
     *
     * Возвращает location_id в качестве id элемента.
     *
     * @return array
     */
    public function actionLocationFloorList(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $buildingId = Yii::$app->request->post('building_id');
        $room       = Yii::$app->request->post('room');

        if (!$buildingId || !$room) {
            return [];
        }

        $locations = Location::find()
            ->where([
                'building_id' => $buildingId,
                'room'        => $room,
                'is_active'   => true,
            ])
            ->orderBy([
                'floor' => SORT_ASC,
            ])
            ->all();

        return array_map(
            static fn(Location $location) => [
                'id'   => $location->id,
                'name' => $location->floor,
            ],
            $locations
        );
    }

}
