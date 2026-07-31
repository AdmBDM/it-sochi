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
 * @var int|null $excludeId
 */

if ($excludeId !== null && $node->id === $excludeId) {return;}

$children = $groupedTree[$node->id] ?? [];
$hasChildren = !empty($children);
$isExpanded = isset($expandedNodes[$node->id]);

$marker = !$hasChildren
    ? '•'
    : ($isExpanded ? '▾' : '▸');

echo Html::beginTag('div', [
    'class' => 'tree-node',
]);

echo Html::tag(
    'div',
    Html::tag(
        'span',
        $marker,
        [
            'class' => 'tree-marker me-1',
        ]
    ) .
    Html::tag(
        'span',
        Html::encode($node->name),
        [
            'class' => 'tree-label',
            'data-id' => $node->id,
            'data-name' => $node->name,
        ]
    ),
    [
        'class' => sprintf(
            'tree-node-header list-group-item list-group-item-action%s',
            $selectedNode?->id === $node->id ? ' active' : ''
        ),
        'data-node-id' => $node->id,
        'data-has-children' => $hasChildren ? 1 : 0,
    ]
);

echo Html::beginTag('div', [
    'class' => sprintf(
        'tree-node-children ms-3 border-start%s',
        $isExpanded ? '' : ' d-none'
    ),
]);

foreach ($children as $child) {

    echo $this->render('_parent_tree_node', [
        'node' => $child,
        'groupedTree' => $groupedTree,
        'selectedNode' => $selectedNode,
        'expandedNodes' => $expandedNodes,
        'excludeId' => $excludeId,
    ]);
}

echo Html::endTag('div');

echo Html::endTag('div');
