<?php

namespace backend\controllers;

use Throwable;
use Yii;
use common\controllers\SochiMainController;
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
}