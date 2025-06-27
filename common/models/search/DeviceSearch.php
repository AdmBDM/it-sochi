<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Device;

class DeviceSearch extends Device
{
    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['id', 'device_model_id', 'workplace_id', 'device_status_id'], 'integer'],
            [['serial', 'inventory', 'note'], 'safe'],
        ];
    }

    /**
     * @param $params
     *
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = Device::find();

        $dataProvider = new ActiveDataProvider(['query' => $query]);
        $this->load($params);

        if (!$this->validate()) return $dataProvider;

        $query->andFilterWhere([
            'id' => $this->id,
            'device_model_id' => $this->device_model_id,
            'workplace_id' => $this->workplace_id,
            'device_status_id' => $this->device_status_id,
        ])->andFilterWhere(['ilike', 'serial', $this->serial])
            ->andFilterWhere(['ilike', 'inventory', $this->inventory])
            ->andFilterWhere(['ilike', 'note', $this->note]);

        return $dataProvider;
    }
}
