<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Workplace;

class WorkplaceSearch extends Workplace
{
    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['id', 'employee_id', 'department_id', 'location_id'], 'integer'],
            [['note'], 'safe'],
        ];
    }

    /**
     * @param $params
     *
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = Workplace::find();

        $dataProvider = new ActiveDataProvider(['query' => $query]);
        $this->load($params);

        if (!$this->validate()) return $dataProvider;

        $query->andFilterWhere([
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'department_id' => $this->department_id,
            'location_id' => $this->location_id,
        ])->andFilterWhere(['ilike', 'note', $this->note]);

        return $dataProvider;
    }
}
