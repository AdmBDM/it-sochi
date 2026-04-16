<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Movement;

class MovementSearch extends Movement
{
    public $deviceName;
    public $oldValueName;
    public $newValueName;
    public $employeeName;

    public function rules(): array
    {
        return [
            [['id', 'device_id', 'moved_by_user_id', 'device_status_id', 'is_active'], 'integer'],
            [['comment', 'type_change', 'moved_at', 'deviceName', 'oldValueName', 'newValueName', 'employeeName'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
//        $query = Movement::find()
//            ->joinWith(['device', 'oldWorkplace', 'newWorkplace', 'oldStatus', 'newStatus', 'employee']);
        $query = Movement::find()
            ->joinWith([
                'device.model.type',
                'device.model.brand',
                'oldWorkplace',
                'newWorkplace',
                'oldStatus',
                'newStatus',
                'employee'
            ]);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['moved_at' => SORT_DESC],
                'attributes' => [
                    'id',
                    'moved_at',
                    'type_change',
//                    'deviceName' => [
//                        'asc' => ['devices.name' => SORT_ASC],
//                        'desc' => ['devices.name' => SORT_DESC],
//                    ],
                    'deviceName' => [
                        'asc' => [
                            'device_types.name' => SORT_ASC,
                            'device_brands.name' => SORT_ASC,
                            'device_models.name' => SORT_ASC,
                        ],
                        'desc' => [
                            'device_types.name' => SORT_DESC,
                            'device_brands.name' => SORT_DESC,
                            'device_models.name' => SORT_DESC,
                        ],
                    ],
                    'oldValueName' => [
                        'asc' => ['workplaces.name' => SORT_ASC, 'device_statuses.name' => SORT_ASC],
                        'desc' => ['workplaces.name' => SORT_DESC, 'device_statuses.name' => SORT_DESC],
                    ],
                    'newValueName' => [
                        'asc' => ['workplaces.name' => SORT_ASC, 'device_statuses.name' => SORT_ASC],
                        'desc' => ['workplaces.name' => SORT_DESC, 'device_statuses.name' => SORT_DESC],
                    ],
                    'employeeName' => [
                        'asc' => ['employees.last_name' => SORT_ASC, 'employees.first_name' => SORT_ASC, 'employees.middle_name' => SORT_ASC],
                        'desc' => ['employees.last_name' => SORT_DESC, 'employees.first_name' => SORT_DESC, 'employees.middle_name' => SORT_DESC],
                    ],
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['movements.id' => $this->id])
            ->andFilterWhere(['movements.type_change' => $this->type_change])
            ->andFilterWhere(['movements.is_active' => $this->is_active])
            ->andFilterWhere(['like', 'workplaces.name', $this->oldValueName])
            ->andFilterWhere(['like', 'workplaces.name', $this->newValueName])
            ->andFilterWhere(['like', 'device_statuses.name', $this->oldValueName])
            ->andFilterWhere(['like', 'device_statuses.name', $this->newValueName])
        ;

        $query->andFilterWhere(['or',
            ['ilike', 'employees.last_name', $this->employeeName],
            ['ilike', 'employees.first_name', $this->employeeName],
            ['ilike', 'employees.middle_name', $this->employeeName],
        ]);

        $query->andFilterWhere(['or',
            ['ilike', 'device_types.name', $this->deviceName],
            ['ilike', 'device_brands.name', $this->deviceName],
            ['ilike', 'device_models.name', $this->deviceName],
            ['ilike', 'devices.serial_number', $this->deviceName],
        ]);


        return $dataProvider;
    }
}
