<?php

namespace backend\controllers;

use common\controllers\SochiMainController;

class DashboardController extends SochiMainController
{
    /**
     * @return string
     */
    public function actionIndex(): string
    {
        return $this->render('index');
    }
}
