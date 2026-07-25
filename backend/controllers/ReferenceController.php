<?php

declare(strict_types=1);

namespace backend\controllers;

use common\controllers\SochiMainController;
use common\models\ReferenceItem;
use common\models\search\SearchReferenceItem;
use Yii;
use yii\db\Exception;
use yii\web\NotFoundHttpException;
use yii\web\Response;

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
            $selectedNode = ReferenceItem::findOne([
                'id' => $id,
                'is_deleted' => false,
            ]);

            if ($selectedNode === null) {
                throw new NotFoundHttpException('Элемент классификатора не найден.');
            }
        }

        $expandedNodes = [];

        if ($selectedNode !== null) {
            foreach ($selectedNode->getPath() as $item) {
                $expandedNodes[$item->id] = true;
            }
        }

        $searchModel = new SearchReferenceItem();

        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            $selectedNode?->id
        );

        // читаем корень дерева
        $rootNodes = ReferenceItem::getRootNodes();

        // читаем сгруппированное дерево
        $groupedTree = ReferenceItem::getGroupedTree();

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
            return $this->redirect([
                'index',
                'id' => $model->parent_id,
            ]);
        }

        $parent = $parent_id !== null
            ? ReferenceItem::findOne($parent_id)
            : null;

        return $this->render('create', [
            'model' => $model,
            'parent' => $parent,
            'parentList' => ReferenceItem::getParentList(),
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
     */
    public function actionUpdate(int $id): string|Response
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect([
                'index',
                'id' => $model->parent_id,
            ]);
        }

        return $this->render('update', [
            'model' => $model,
            'parent' => $model->parent,
            'parentList' => ReferenceItem::getParentList(),
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

            return $this->redirect([
                'index',
                'id' => $model->id,
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
     * @throws NotFoundHttpException
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

        } catch (\Throwable $e) {

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
     * @throws NotFoundHttpException
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

        } catch (\Throwable $e) {

            $transaction->rollBack();

            throw $e;
        }

        return $this->redirect([
            'index',
            'id' => $model->parent_id,
        ]);
    }

}
