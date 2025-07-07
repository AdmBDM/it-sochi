<?php

namespace backend\controllers;

use common\controllers\SochiMainController;
use common\models\Department;
use common\models\Device;
use common\models\Employee;
use common\models\Movement;
use common\models\Organization;
use common\models\Workplace;
use common\models\search\MovementSearch;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * MovementController implements the CRUD actions for Movement model.
 */
class MovementController extends SochiMainController
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
     * Lists all Movement models.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new MovementSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Movement model.
     *
     * @param int $id ID
     *
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
     * Creates a new Movement model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return string|Response
     * @throws Exception
     */
    public function actionCreate(): Response|string
    {
        $model = new Movement();

        $devices = ArrayHelper::map(Device::find()->all(), 'id', 'name'); // или другое поле
        $employees = ArrayHelper::map(Employee::find()->all(), 'id', 'fullname');
        $workplaces = ArrayHelper::map(Workplace::find()->all(), 'id', 'label');
        $organizations = ArrayHelper::map(Organization::find()->all(), 'id', 'name');
        $departments = ArrayHelper::map(Department::find()->all(), 'id', 'name');

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'devices' => $devices,
            'employees' => $employees,
            'workplaces' => $workplaces,
            'organizations' => $organizations,
            'departments' => $departments,
        ]);
    }

    /**
     * Updates an existing Movement model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param int $id ID
     *
     * @return string|Response
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id): Response|string
    {
        $model = $this->findModel($id);

        $devices = ArrayHelper::map(Device::find()->all(), 'id', 'name'); // или другое поле
        $employees = ArrayHelper::map(Employee::find()->all(), 'id', 'fullname');
        $workplaces = ArrayHelper::map(Workplace::find()->all(), 'id', 'label');
        $organizations = ArrayHelper::map(Organization::find()->all(), 'id', 'name');
        $departments = ArrayHelper::map(Department::find()->all(), 'id', 'name');

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'devices' => $devices,
            'employees' => $employees,
            'workplaces' => $workplaces,
            'organizations' => $organizations,
            'departments' => $departments,
        ]);
    }

    /**
     * Deletes an existing Movement model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param int $id ID
     *
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete(int $id): Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Movement model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $id ID
     *
     * @return Movement the loaded model
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): Movement
    {
        if (($model = Movement::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
