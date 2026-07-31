<?php

declare(strict_types=1);

use common\models\ReferenceItem;

/**
 * @var yii\web\View $this
 * @var ReferenceItem|null $selectedNode
 * @var array<int|null, ReferenceItem[]> $groupedTree
 * @var array<int,bool> $expandedNodes
 * @var int|null $excludeId
 */

$this->registerJsFile(
        '@web/js/reference-parent-tree.js',
        [
                'depends' => [
                        \yii\web\JqueryAsset::class,
                ],
        ]
);

$rootNodes = ReferenceItem::getTree();
?>

<div class="list-group">

    <?php if (empty($rootNodes)): ?>

        <div class="list-group-item text-muted">
            Нет данных.
        </div>

    <?php else: ?>

        <?php foreach ($rootNodes as $node): ?>

            <?=
            $this->render('_parent_tree_node', [
                'node'          => $node,
                'selectedNode'  => $selectedNode,
                'groupedTree'   => $groupedTree,
                'expandedNodes' => $expandedNodes,
                'excludeId'     => $excludeId,
            ])
            ?>

        <?php endforeach; ?>

    <?php endif; ?>

</div>
