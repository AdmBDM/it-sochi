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
    public $deviceTypeName;
    public $deviceBrandName;
    public $deviceModelName;
    public $employeeFullName;

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['id', 'model_id', 'status_id', 'workplace_id'], 'integer'],
            [
                [
                    'serial_number',
                    'inventory_number',
                    'name',
                    'created_at',
                    'updated_at',
                    'deviceTypeName',
                    'deviceBrandName',
                    'deviceModelName',
                    'employeeFullName'
                ],
                'safe'
            ],
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
            ->joinWith([
                'model brand', // alias support
                'model.type',
                'workplace.employee'
            ])
            ->with([
                'status',
                'workplace.location'
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['updated_at' => SORT_DESC]],
            'pagination' => ['pageSize' => 20],
        ]);

        $dataProvider->sort->attributes['deviceModelName'] = [
            'asc' => ['device_models.name' => SORT_ASC],
            'desc' => ['device_models.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['deviceBrandName'] = [
            'asc' => ['device_brands.name' => SORT_ASC],
            'desc' => ['device_brands.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['deviceTypeName'] = [
            'asc' => ['device_types.name' => SORT_ASC],
            'desc' => ['device_types.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['employeeFullName'] = [
            'asc' => ['employees.full_name' => SORT_ASC],
            'desc' => ['employees.full_name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'devices.id' => $this->id,
            'devices.model_id' => $this->model_id,
            'devices.status_id' => $this->status_id,
            'devices.workplace_id' => $this->workplace_id,
            'devices.created_at' => $this->created_at,
            'devices.updated_at' => $this->updated_at,
        ]);

        $query
            ->andFilterWhere(['ilike', 'devices.serial_number', $this->serial_number])
            ->andFilterWhere(['ilike', 'devices.inventory_number', $this->inventory_number])
            ->andFilterWhere(['ilike', 'devices.name', $this->name])
            ->andFilterWhere(['ilike', 'device_models.name', $this->deviceModelName])
            ->andFilterWhere(['ilike', 'device_brands.name', $this->deviceBrandName])
            ->andFilterWhere(['ilike', 'device_types.name', $this->deviceTypeName])
            ->andFilterWhere(['ilike', 'employees.full_name', $this->employeeFullName]);

        return $dataProvider;
    }
}
