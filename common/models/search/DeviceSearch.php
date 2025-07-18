<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Device;

/**
 * DeviceSearch represents the model behind the search form of `common\models\Device`.
 */
class DeviceSearch extends Device
{
    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['id', 'model_id', 'status_id', 'workplace_id'], 'integer'],
            [['serial_number', 'inventory_number', 'name', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function scenarios(): array
    {
        return Model::scenarios();
    }

    /**
     * @param $params
     *
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = Device::find()
            ->with(['model.brand', 'model.type', 'status', 'workplace.location', 'workplace.employee']);

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
            'model_id' => $this->model_id,
            'status_id' => $this->status_id,
            'workplace_id' => $this->workplace_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['ilike', 'serial_number', $this->serial_number])
            ->andFilterWhere(['ilike', 'inventory_number', $this->inventory_number])
            ->andFilterWhere(['ilike', 'name', $this->name]);

        return $dataProvider;
    }
}
