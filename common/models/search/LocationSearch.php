<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Location;

class LocationSearch extends Location
{
    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['id', 'building_id', 'floor'], 'integer'],
            [['room'], 'safe'],
        ];
    }

    /**
     * @param $params
     *
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = Location::find();

        $dataProvider = new ActiveDataProvider(['query' => $query]);
        $this->load($params);

        if (!$this->validate()) return $dataProvider;

        $query->andFilterWhere([
            'id' => $this->id,
            'building_id' => $this->building_id,
            'floor' => $this->floor,
        ])->andFilterWhere(['ilike', 'room', $this->room]);

        return $dataProvider;
    }
}
