<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Movement;

class MovementSearch extends Movement
{
    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['id', 'device_id', 'from_workplace_id', 'to_workplace_id', 'moved_by_user_id'], 'integer'],
            [['moved_at', 'note'], 'safe'],
        ];
    }

    /**
     * @param $params
     *
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = Movement::find();

        $dataProvider = new ActiveDataProvider(['query' => $query, 'sort' => ['defaultOrder' => ['moved_at' => SORT_DESC]]]);
        $this->load($params);

        if (!$this->validate()) return $dataProvider;

        $query->andFilterWhere([
            'id' => $this->id,
            'device_id' => $this->device_id,
            'from_workplace_id' => $this->from_workplace_id,
            'to_workplace_id' => $this->to_workplace_id,
            'moved_by_user_id' => $this->moved_by_user_id,
            'moved_at' => $this->moved_at,
        ])->andFilterWhere(['ilike', 'note', $this->note]);

        return $dataProvider;
    }
}
