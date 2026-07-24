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
        ]);
    }

}
