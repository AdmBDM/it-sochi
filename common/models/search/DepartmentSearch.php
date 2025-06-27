<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Department;

class DepartmentSearch extends Department
{
    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['id', 'organization_id'], 'integer'],
            [['name'], 'safe'],
        ];
    }

    /**
     * @param $params
     *
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = Department::find();

        $dataProvider = new ActiveDataProvider(['query' => $query]);
        $this->load($params);

        if (!$this->validate()) return $dataProvider;

        $query->andFilterWhere(['id' => $this->id, 'organization_id' => $this->organization_id])
            ->andFilterWhere(['ilike', 'name', $this->name]);

        return $dataProvider;
    }
}
