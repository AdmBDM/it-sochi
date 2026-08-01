<?php

declare(strict_types=1);

use common\models\ReferenceItem;
use yii\base\InvalidConfigException;
use yii\web\JqueryAsset;

/**
 * @var yii\web\View $this
 * @var ReferenceItem|null $selectedNode
 * @var array<int|null, ReferenceItem[]> $groupedTree
 * @var array<int,bool> $expandedNodes
 * @var int|null $excludeId
 * @var string $target
 * @var string|null $rootCode
 */

try {
    $this->registerJsFile(
            '@web/js/reference-parent-tree.js',
            [
                    'depends' => [
                            JqueryAsset::class,
                    ],
            ]
    );
} catch (InvalidConfigException $e) {

}

if ($rootCode === null) {
    $rootNodes = ReferenceItem::getTree();
} else {

    $root = ReferenceItem::getRoot($rootCode);

    $rootNodes = $root === null
            ? []
            : [$root];
}
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
                'target'        => $target,
                'rootCode'      => $rootCode,
            ])
            ?>

        <?php endforeach; ?>

    <?php endif; ?>

</div>
