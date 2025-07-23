<?php

namespace backend\controllers;

use common\models\CartridgeTransfer;
use common\models\search\CartridgeTransferSearch;
use Throwable;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * CartridgeTransferController implements the CRUD actions for CartridgeTransfer model.
 */
class CartridgeTransferController extends Controller
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all CartridgeTransfer models.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new CartridgeTransferSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CartridgeTransfer model.
     * @param int $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CartridgeTransfer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     * @throws Exception
     */
    public function actionCreate(): Response|string
    {
        $model = new CartridgeTransfer();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing CartridgeTransfer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
     * @return Response|string
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id): Response|string
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing CartridgeTransfer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDelete(int $id): Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the CartridgeTransfer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return CartridgeTransfer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): CartridgeTransfer
    {
        if (($model = CartridgeTransfer::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
