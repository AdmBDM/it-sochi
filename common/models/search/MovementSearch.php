<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Movement;

/**
 * MovementSearch represents the model behind the search form of `common\models\Movement`.
 */
class MovementSearch extends Movement
{
    public $id;
    public $device_id;
    public $employee_id;
    public $from_workplace_id;
    public $to_workplace_id;
    public $organization_id;
    public $department_id;
    public $comment;
    public $moved_at;
    public $created_at;
    public $updated_at;
    public $deviceName;
    public $employeeName;
    public $fromWorkplaceLabel;
    public $toWorkplaceLabel;
    public $organizationName;
    public $departmentName;

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['id', 'device_id', 'employee_id', 'from_workplace_id', 'to_workplace_id', 'organization_id', 'department_id'], 'integer'],
            [['comment', 'moved_at', 'created_at', 'updated_at'], 'safe'],
            [['deviceName', 'employeeName', 'fromWorkplaceLabel', 'toWorkplaceLabel', 'organizationName', 'departmentName'], 'safe'],
        ];
    }

    /**
     * @return array|array[]
     */
    public function scenarios(): array
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
    public function search(array $params, string $formName = null): ActiveDataProvider
    {
        $query = Movement::find()->joinWith([
            'device d',
            'employee e',
            'fromWorkplace fw',
            'toWorkplace tw',
//            'organization o',
//            'department dep',
        ]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['deviceName'] = [
            'asc' => ['d.name' => SORT_ASC],
            'desc' => ['d.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['employeeName'] = [
            'asc' => ['e.full_name' => SORT_ASC],
            'desc' => ['e.full_name' => SORT_DESC],
//            'asc' => ['e.lastname' => SORT_ASC, 'e.firstname' => SORT_ASC],
//            'desc' => ['e.lastname' => SORT_DESC, 'e.firstname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['fromWorkplaceLabel'] = [
            'asc' => ['fw.label' => SORT_ASC],
            'desc' => ['fw.label' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['toWorkplaceLabel'] = [
            'asc' => ['tw.label' => SORT_ASC],
            'desc' => ['tw.label' => SORT_DESC],
        ];

//        $dataProvider->sort->attributes['organizationName'] = [
//            'asc' => ['o.name' => SORT_ASC],
//            'desc' => ['o.name' => SORT_DESC],
//        ];

//        $dataProvider->sort->attributes['departmentName'] = [
//            'asc' => ['dep.name' => SORT_ASC],
//            'desc' => ['dep.name' => SORT_DESC],
//        ];

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'device_id' => $this->device_id,
            'from_workplace_id' => $this->from_workplace_id,
            'to_workplace_id' => $this->to_workplace_id,
            'moved_at' => $this->moved_at,
            'moved_by_user_id' => $this->moved_by_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['ilike', 'comment', $this->comment])
            ->andFilterWhere(['ilike', 'd.name', $this->deviceName])
            ->andFilterWhere(['ilike', 'e.full_name', $this->employeeName])
            ->andFilterWhere(['ilike', 'fw.label', $this->fromWorkplaceLabel])
            ->andFilterWhere(['ilike', 'tw.label', $this->toWorkplaceLabel])
//            ->andFilterWhere(['ilike', 'o.name', $this->organizationName])
//            ->andFilterWhere(['ilike', 'dep.name', $this->departmentName])
        ;

        return $dataProvider;
    }
}
