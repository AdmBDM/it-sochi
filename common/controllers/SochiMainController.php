<?php

namespace common\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;

class SochiMainController extends Controller
{
    public $layout = '@app/views/layouts/it-sochi.php';

    public function behaviors(): array
    {
        return [
            // Подключаем фильтр доступа сразу
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create', 'update', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => fn() => Yii::$app->user->identity?->canEdit() ?? false,
                    ],
                ],
            ],
        ];
    }
}
