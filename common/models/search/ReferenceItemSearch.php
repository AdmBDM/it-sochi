<?php

declare(strict_types=1);

namespace common\models\search;

use common\models\ReferenceItem;
use yii\data\ActiveDataProvider;

/**
 * Поиск элементов универсального классификатора.
 */
class ReferenceItemSearch extends ReferenceItem
{
    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
//            [['id', 'parent_id', 'type_id', 'sort_order'], 'integer'],
            [['id', 'parent_id', 'type_id'], 'integer'],
            [['code', 'name', 'description'], 'safe'],
            [['is_active'], 'boolean'],
        ];
    }

    /**
     * @return array
     */
    public function scenarios(): array
    {
        return parent::scenarios();
    }

    /**
     * Поиск элементов классификатора.
     *
     * @param array $params
     * @param int|null $parentId
     *
     * @return ActiveDataProvider
     */
    public function search(array $params = [], ?int $parentId = null): ActiveDataProvider
    {
        $query = ReferenceItem::find()
            ->andWhere(['is_deleted' => false]);

        if ($parentId === null) {
            $query->andWhere(['parent_id' => null]);
        } else {
            $query->andWhere(['parent_id' => $parentId]);
        }

        $query->orderBy([
            'sort_order' => SORT_ASC,
            'name' => SORT_ASC,
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

//        $query->andFilterWhere([
//            'id' => $this->id,
//            'type_id' => $this->type_id,
//            'is_active' => $this->is_active,
//        ]);
        $query->andFilterWhere([
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'type_id' => $this->type_id,
            'is_active' => $this->is_active,
        ]);

        $query->andFilterWhere(['ilike', 'code', $this->code]);
        $query->andFilterWhere(['ilike', 'name', $this->name]);
        $query->andFilterWhere(['ilike', 'description', $this->description]);

        return $dataProvider;
    }
}
