<?php

namespace backend\controllers;

use common\controllers\SochiMainController;
use yii\filters\AccessControl;

class DashboardController extends SochiMainController
{
    /**
     * @return array[]
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', /* другие действия */],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],  // только авторизованные пользователи
                    ],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        return $this->render('index');
    }
}
