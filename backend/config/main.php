<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
			'csrfParam' => '_csrf-backend',
			'baseUrl' => '/admin',
		],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['//site/login'], // <- ВАЖНО! с двумя слешами — обращение к frontend
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
			'name' => 'advanced-backend',
			'cookieParams' => ['path' => '/'],
		],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [['class' => \yii\log\FileTarget::class, 'levels' => ['error', 'warning']]],
        ],
        'errorHandler' => ['errorAction' => 'site/error'],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
        // Для генерации ссылок на frontend из backend
        'urlManagerFrontend' => [
            'class' => 'yii\web\UrlManager',
            'hostInfo' => 'http://it-sochi',
            'baseUrl' => '',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
    ],
    'params' => $params,
];
