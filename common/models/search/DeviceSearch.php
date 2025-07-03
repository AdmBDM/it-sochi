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
            [['id', 'workplace_id', 'type_id', 'brand_id', 'model_id', 'status_id'], 'integer'],
            [['serial_number', 'inventory_number', 'comment', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = Device::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'workplace_id' => $this->workplace_id,
            'type_id' => $this->type_id,
            'brand_id' => $this->brand_id,
            'model_id' => $this->model_id,
            'status_id' => $this->status_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['ilike', 'serial_number', $this->serial_number])
            ->andFilterWhere(['ilike', 'inventory_number', $this->inventory_number])
            ->andFilterWhere(['ilike', 'comment', $this->comment]);

        return $dataProvider;
    }
}
