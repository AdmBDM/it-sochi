<?php

namespace common\models\trassir;

use Yii;

abstract class ActiveRecord extends \yii\db\ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->trassirDb;
    }
}
