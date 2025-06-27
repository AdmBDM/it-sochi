<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\DeviceModel;

class DeviceModelSearch extends DeviceModel
{
    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['id', 'device_type_id', 'device_brand_id'], 'integer'],
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
        $query = DeviceModel::find();

        $dataProvider = new ActiveDataProvider(['query' => $query]);
        $this->load($params);

        if (!$this->validate()) return $dataProvider;

        $query->andFilterWhere([
            'id' => $this->id,
            'device_type_id' => $this->device_type_id,
            'device_brand_id' => $this->device_brand_id,
        ])->andFilterWhere(['ilike', 'name', $this->name]);

        return $dataProvider;
    }
}
