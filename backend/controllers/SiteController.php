<?php

namespace backend\controllers;

use common\models\LoginForm;
use common\controllers\SochiMainController;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * Site controller
 */
//class SiteController extends Controller
class SiteController extends SochiMainController
{
    /**
     * @return array[]
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @return array[]
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string|Response
     */
    public function actionLogin(): Response|string
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'blank';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return \yii\console\Response|Response
     * @throws \yii\base\InvalidRouteException
     */
    public function actionLogout(): Response|\yii\console\Response
    {
        Yii::$app->user->logout();

//        return $this->goHome();
//        return Yii::$app->getResponse()->redirect(Yii::$app->urlManager->hostInfo . '/');  // ������� �� frontend

//        return $this->redirect('http://it-sochi/'); // локальный адрес фронта
        return $this->redirect(Yii::$app->params['frontendUrl'] ?? 'http://it-sochi/');
    }
}
