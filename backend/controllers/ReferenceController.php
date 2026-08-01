<?php

declare(strict_types=1);

namespace backend\controllers;

use common\controllers\SochiMainController;
use common\models\ReferenceItem;
use common\models\search\ReferenceItemSearch;
use Throwable;
use Yii;
use yii\db\Exception;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 * Контроллер управления универсальным классификатором ReferenceItem.
 */
//class ReferenceController extends Controller
class ReferenceController extends SochiMainController
{
    /**
     * Главная страница классификатора.
     *
     * @param int|null $id Идентификатор выбранного узла.
     *
     * @return string
     *
     * @throws NotFoundHttpException
     */
    public function actionIndex(?int $id = null): string
    {
        $selectedNode = null;

        if ($id !== null) {

            $params = Yii::$app->request->get(
                'ReferenceItemSearch',
                []
            );

            $showDeleted = (bool)($params['showDeleted'] ?? false);

            $query = ReferenceItem::find()
                ->andWhere([
                    'id' => $id,
                ]);

            if (!$showDeleted) {
                $query->andWhere([
                    'is_deleted' => false,
                ]);
            }

            $selectedNode = $query->one();

            if ($selectedNode === null) {
                throw new NotFoundHttpException(
                    'Элемент классификатора не найден.'
                );
            }
        }

        $expandedNodes = [];

        if ($selectedNode !== null) {
            foreach ($selectedNode->getPath() as $item) {
                $expandedNodes[$item->id] = true;
            }
        }

        $searchModel = new ReferenceItemSearch();
        $searchModel->load(Yii::$app->request->queryParams);

        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            $selectedNode?->id
        );

        // читаем корень дерева
//        $rootNodes = ReferenceItem::getRootNodes($searchModel->showDeleted);
        $rootNodes = ReferenceItem::getTree(null, $searchModel->showDeleted);

        // читаем сгруппированное дерево
        $groupedTree = ReferenceItem::getGroupedTree(null, $searchModel->showDeleted);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'selectedNode' => $selectedNode,
            'rootNodes' => $rootNodes,
            'groupedTree' => $groupedTree,
            'expandedNodes' => $expandedNodes,
        ]);
    }

    /**
     * Создание элемента классификатора.
     *
     * @param int|null $parent_id
     *
     * @return string|Response
     * @throws Exception
     */
    public function actionCreate(?int $parent_id = null): string|Response
    {
        $model = new ReferenceItem();
        $model->parent_id = $parent_id;
        $model->sort_order = 0;
        $model->is_active = true;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (Yii::$app->request->isAjax) {
                return $this->asJson([
                    'success' => true,
                ]);
            }

            return $this->redirect([
                'index',
                'id' => $model->parent_id,
            ]);
        }

        $parent = $parent_id !== null
            ? ReferenceItem::findOne($parent_id)
            : null;

        $parentList = ReferenceItem::getParentList();
        $typeList = ReferenceItem::getTypeList();

        if (Yii::$app->request->isAjax) {

            return $this->renderAjax('create', [
                'model' => $model,
                'parent' => $parent,
                'parentList' => $parentList,
                'typeList' => $typeList,
            ]);
        }

        return $this->render('create', [
            'model' => $model,
            'parent' => $parent,
            'parentList' => $parentList,
            'typeList' => $typeList,
        ]);
    }

    /**
     * Возвращает элемент классификатора.
     *
     * @param int $id
     *
     * @return ReferenceItem
     *
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): ReferenceItem
    {
        $model = ReferenceItem::find()
            ->where([
                'id' => $id,
                'is_deleted' => false,
            ])
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException('Элемент классификатора не найден.');
        }

        return $model;
    }

    /**
     * Редактирование элемента классификатора.
     *
     * @param int $id
     *
     * @return string|Response
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id): string|Response
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            if (Yii::$app->request->isAjax) {

                return $this->asJson([
                    'success' => true,
                ]);
            }

            return $this->redirect([
                'index',
                'id' => $model->parent_id,
            ]);
        }

        $parent = null;
        if (!empty($model->parent_id)) {
            $parent = ReferenceItem::findOne((int)$model->parent_id);
        }

        $parentList = ReferenceItem::getParentList($model->id);
        $typeList = ReferenceItem::getTypeList();

        if (Yii::$app->request->isAjax || Yii::$app->request->isPost) {

            return $this->renderAjax('update', [
                'model' => $model,
                'parent' => $parent,
                'parentList' => $parentList,
                'typeList' => $typeList,
            ]);
        }

        return $this->render('update', [
            'model' => $model,
            'parent' => $parent,
            'parentList' => $parentList,
            'typeList' => $typeList,
        ]);
    }

    /**
     * Логическое удаление элемента классификатора.
     *
     * @param int $id
     *
     * @return Response
     *
     * @throws NotFoundHttpException|Exception
     */
    public function actionDelete(int $id): Response
    {
        $model = $this->findModel($id);

        // проверка удаления непустого элемента
        $count = $model->getActiveChildrenCount();
        if ($count > 0) {
            Yii::$app->session->setFlash(
                'error',
                sprintf(
                    'Удаление невозможно. Элемент содержит дочерние элементы - %d.',
                    $count
                )
            );

            if (Yii::$app->request->isAjax) {
                return $this->asJson([
                    'success' => true,
                ]);
            }

            return $this->redirect([
                'index',
                'id' => $model->parent_id,
            ]);
        }

        $model->is_deleted = true;
        $model->save(false, ['is_deleted']);

        return $this->redirect([
            'index',
            'id' => $model->parent_id,
        ]);
    }

    /**
     * Перемещает элемент вверх среди элементов одного родителя.
     *
     * @param int $id
     *
     * @return Response
     *
     * @throws Exception
     * @throws NotFoundHttpException
     * @throws Throwable
     */
    public function actionMoveUp(int $id): Response
    {
        $model = $this->findModel($id);

        $previous = ReferenceItem::find()
            ->where([
                'parent_id' => $model->parent_id,
                'is_deleted' => false,
            ])
            ->andWhere(['<', 'sort_order', $model->sort_order])
            ->orderBy([
                'sort_order' => SORT_DESC,
                'id' => SORT_DESC,
            ])
            ->one();

        if ($previous === null) {
            return $this->redirect([
                'index',
                'id' => $model->parent_id,
            ]);
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {

            $currentOrder = $model->sort_order;

            $model->sort_order = $previous->sort_order;
            $previous->sort_order = $currentOrder;

            $model->save(false, ['sort_order']);
            $previous->save(false, ['sort_order']);

            $transaction->commit();

        } catch (Throwable $e) {

            $transaction->rollBack();

            throw $e;
        }

        return $this->redirect([
            'index',
            'id' => $model->parent_id,
        ]);
    }

    /**
     * Перемещает элемент вниз среди элементов одного родителя.
     *
     * @param int $id
     *
     * @return Response
     *
     * @throws Exception
     * @throws NotFoundHttpException
     * @throws Throwable
     */
    public function actionMoveDown(int $id): Response
    {
        $model = $this->findModel($id);

        $next = ReferenceItem::find()
            ->where([
                'parent_id' => $model->parent_id,
                'is_deleted' => false,
            ])
            ->andWhere(['>', 'sort_order', $model->sort_order])
            ->orderBy([
                'sort_order' => SORT_ASC,
                'id' => SORT_ASC,
            ])
            ->one();

        if ($next === null) {
            return $this->redirect([
                'index',
                'id' => $model->parent_id,
            ]);
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {

            $currentOrder = $model->sort_order;

            $model->sort_order = $next->sort_order;
            $next->sort_order = $currentOrder;

            $model->save(false, ['sort_order']);
            $next->save(false, ['sort_order']);

            $transaction->commit();

        } catch (Throwable $e) {

            $transaction->rollBack();

            throw $e;
        }

        return $this->redirect([
            'index',
            'id' => $model->parent_id,
        ]);
    }

    /**
     * Просмотр элемента классификатора.
     *
     * @param int $id
     *
     * @return string
     *
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Восстанавливает ранее логически удалённый элемент классификатора.
     *
     * @param int $id Идентификатор восстанавливаемого элемента.
     *
     * @return Response
     * @throws Exception
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionRestore(int $id): Response
    {
        $model = ReferenceItem::find()
            ->where([
                'id' => $id,
            ])
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException(
                'Элемент классификатора не найден.'
            );
        }

        $model->is_deleted = false;

        if (!$model->save(true, ['is_deleted'])) {
            throw new ServerErrorHttpException(
                'Не удалось восстановить запись.'
            );
        }

        if (Yii::$app->request->isAjax) {

            return $this->asJson([
                'success' => true,
            ]);
        }

        return $this->redirect([
            'index',
            'id' => $model->parent_id,
        ]);
    }

    /**
     * Возвращает модальное окно выбора родительского элемента.
     *
     * @param int|null $selectedId Идентификатор текущего выбранного родителя.
     * @param int|null $excludeId Идентификатор элемента, который необходимо исключить
     *                            из дерева (редактируемый элемент).
     * @param string $target
     * @param string|null $rootCode
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionParentSelector(
        ?int $selectedId = null,
        ?int $excludeId = null,
        string $target = 'parent_id',
        ?string $rootCode = null
    ): string
    {

        $selectedNode = null;

        if ($selectedId !== null) {

            $selectedNode = ReferenceItem::findOne([
                'id' => $selectedId,
            ]);

            if ($selectedNode === null) {
                throw new NotFoundHttpException(
                    'Элемент классификатора не найден.'
                );
            }
        }

        $expandedNodes = [];

        if ($selectedNode !== null) {

            foreach ($selectedNode->getPath() as $item) {
                $expandedNodes[$item->id] = true;
            }
        }

        return $this->renderAjax('_parent_tree', [
            'selectedNode'  => $selectedNode,
            'groupedTree'   => ReferenceItem::getGroupedTree($rootCode),
            'expandedNodes' => $expandedNodes,
            'excludeId'     => $excludeId,
            'target'        => $target,
            'rootCode'      => $rootCode,
        ]);
    }


    /**
     * Метод проверки методов
     * @param int $id
     *
     * @return string
     */
    public function actionTestRoot(int $id): string
    {
        return sprintf(
            "Метод для текущих проверок: %s (%d)\n",
            '!', $id
        );
    }

}
