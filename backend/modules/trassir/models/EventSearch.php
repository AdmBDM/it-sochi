<?php

namespace backend\modules\trassir\models;

use common\models\trassir\EventLog;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class EventSearch extends Model
{
    public ?string $user = null;
    public ?string $dateFrom = null;
    public ?string $dateTo = null;
    private const int PAGE_SIZE = 20;

    public function rules(): array
    {
        return [
            [['user'], 'string'],
            [['dateFrom', 'dateTo'], 'safe'],
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = EventLog::find();
        self::applyCommonFilter($query);

        $this->load($params);

        if (!$this->validate()) {
            return new ActiveDataProvider([
                'query' => $query->where('0=1'),
            ]);
        }

        if ($this->user !== null && $this->user !== '') {
            $query->andWhere(['p2' => $this->user]);
        }

        if (!empty($this->dateFrom)) {
            $query->andWhere([
                '>=',
                'ts',
                strtotime($this->dateFrom . ' 00:00:00') * 1000000,
            ]);
        }

        if (!empty($this->dateTo)) {
            $query->andWhere([
                '<=',
                'ts',
                strtotime($this->dateTo . ' 23:59:59') * 1000000,
            ]);
        }

        $query->orderBy(['ts' => SORT_DESC]);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => self::PAGE_SIZE,
            ],
            'sort' => [
                'defaultOrder' => [
                    'ts' => SORT_DESC,
                ],
            ],
        ]);
    }

    public static function getUsers(): array
    {
        $query = EventLog::find()
            ->select(['p2', 'p1'])
            ->distinct()
            ->orderBy(['p1' => SORT_ASC]);

        self::applyCommonFilter($query);

        $rows = $query
            ->asArray()
            ->all();

        return array_column($rows, 'p1', 'p2');
    }

    private static function applyCommonFilter($query): void
    {
        $query
            ->andWhere(['not', ['p2' => null]])
            ->andWhere(['<>', 'p2', ''])
            ->andWhere("p2 !~ '^[0-9]{1,3}(\\.[0-9]{1,3}){3}$'");
    }
}
