<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Workplace;

/**
 * WorkplaceSearch — поисковая модель для рабочего места.
 */
class WorkplaceSearch extends Workplace
{
    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['id', 'employee_id', 'location_id', 'department_id'], 'integer'],
            [['comment', 'name', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function scenarios(): array
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param $params
     *
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = Workplace::find()
            ->with(['employee', 'location', 'department']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['updated_at' => SORT_DESC]],
            'pagination' => ['pageSize' => 20],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'location_id' => $this->location_id,
            'department_id' => $this->department_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['ilike', 'name', $this->name]);

        return $dataProvider;
    }
}
