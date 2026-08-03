<?php

namespace backend\modules\trassir\models;

use common\models\trassir\PacsEvent;
use yii\data\ActiveDataProvider;

class PacsEventSearch
{
    public function search(
        ?int $from,
        ?int $to,
        int $selectedTs
    ): ActiveDataProvider
    {
        $query = PacsEvent::find();

        if ($from !== null) {
            $query->andWhere(['>=', 'event_ts', $from]);
        }

        if ($to !== null) {
            $query->andWhere(['<=', 'event_ts', $to]);
        }

        $query->andWhere([
            'event_ts' => $selectedTs,
        ]);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
            ],
            'sort' => [
                'defaultOrder' => [
                    'event_ts' => SORT_ASC,
                ],
            ],
        ]);
    }
}
