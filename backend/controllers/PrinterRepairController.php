<?php
namespace backend\controllers;

use common\controllers\SochiMainController;
use Yii;
use common\models\PrinterRepair;
use common\models\Device;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

//class PrinterRepairController extends Controller
class PrinterRepairController extends SochiMainController
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
        ];
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => PrinterRepair::find()->with('device'),
            'sort' => ['defaultOrder' => ['started_at' => SORT_DESC]],
        ]);

        return $this->render('index', [
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
     * @param null $device_id
     *
     * @return string|Response
     * @throws Exception
     */
    public function actionCreate(null $device_id = null): Response|string
    {
        $model = new PrinterRepair();
        $model->started_at = date('Y-m-d');

        if ($device_id) {
            $model->device_id = $device_id;
        }

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Ремонт добавлен');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'printers' => $this->getPrintersList(),
        ]);
    }

    /**
     * @param $id
     *
     * @return string|Response
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id): Response|string
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Изменения сохранены');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'printers' => $this->getPrintersList(),
        ]);
    }

    /**
     * @param $id
     *
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id): Response
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Запись удалена');
        return $this->redirect(['index']);
    }

    /**
     * @param $id
     *
     * @return PrinterRepair|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id): ?PrinterRepair
    {
        if (($model = PrinterRepair::findOne(['id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Страница не найдена');
    }

    /**
     * @return array
     */
    private function getPrintersList(): array
    {
        return Device::find()
            ->joinWith('model.type')
            ->where([
                'OR',
                ['device_types.name' => 'Принтер'],
                ['device_types.name' => 'МФУ']
            ])
            ->andWhere(['devices.is_active' => true])
            ->all();
    }
}
