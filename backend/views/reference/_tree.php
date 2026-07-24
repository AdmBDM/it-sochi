<?php

declare(strict_types=1);

use common\models\ReferenceItem;

/**
 * @var yii\web\View $this
 * @var ReferenceItem[] $rootNodes
 * @var array<int|null, ReferenceItem[]> $groupedTree
 * @var ReferenceItem|null $selectedNode
 * @var array<int,bool> $expandedNodes
 */
?>

<div class="card h-100">

    <div class="card-header">
        Дерево классификатора
    </div>

    <div class="list-group list-group-flush">

        <?php if (empty($rootNodes)): ?>

            <div class="list-group-item text-muted">
                Нет данных.
            </div>

        <?php else: ?>

            <?php foreach ($rootNodes as $node): ?>

                <?= $this->render('_tree_node', [
                        'node' => $node,
                        'groupedTree' => $groupedTree,
                        'selectedNode' => $selectedNode,
                        'expandedNodes' => $expandedNodes,
                ]) ?>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</div>
