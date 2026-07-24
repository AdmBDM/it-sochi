<?php

declare(strict_types=1);

use common\models\ReferenceItem;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var ReferenceItem $node
 * @var array<int|null, ReferenceItem[]> $groupedTree
 * @var ReferenceItem|null $selectedNode
 * @var array<int,bool> $expandedNodes
 */

$isActive = $selectedNode?->id === $node->id;
$isExpanded = isset($expandedNodes[$node->id]);
if (!$node->hasChildren()) {
    $marker = '•';
} elseif ($isExpanded) {
    $marker = '▾';
} else {
    $marker = '▸';
}

echo Html::a(
    sprintf(
        '%s %s',
        $marker,
        Html::encode($node->name)
    ),
    ['index', 'id' => $node->id],
    [
        'class' => sprintf(
            'list-group-item list-group-item-action%s',
            $isActive ? ' active' : ''
        ),
        'encode' => false,
    ]
);

$children = $groupedTree[$node->id] ?? [];

if ($children !== [] && $isExpanded) {
    echo Html::beginTag('div', [
        'class' => 'ms-3 border-start',
    ]);

    foreach ($children as $child) {
        echo $this->render('_tree_node', [
            'node' => $child,
            'groupedTree' => $groupedTree,
            'selectedNode' => $selectedNode,
            'expandedNodes' => $expandedNodes,
        ]);
    }

    echo Html::endTag('div');
}
