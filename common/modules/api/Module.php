<?php

namespace common\modules\api;

class Module extends \yii\base\Module
{
    /**
     * @return void
     */
    public function init(): void
    {
        parent::init();
        $this->controllerNamespace = 'common\modules\api\controllers';
    }
}