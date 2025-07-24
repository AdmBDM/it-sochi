<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'language' => 'ru-RU',
    'sourceLanguage' => 'ru-RU',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        // URL‑менеджер для backend (основной)
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
        // URL‑менеджер для frontend — абсолютные ссылки
        'urlManagerFrontend' => [
            'class' => 'yii\web\UrlManager',
            'hostInfo' => 'http://it-sochi',
            'baseUrl' => '',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => require(__DIR__ . '/rules/frontend-rules.php'),
        ],
        // URL‑менеджер для backend — абсолютные ссылки (при необходимости)
        'urlManagerBackend' => [
            'class' => 'yii\web\UrlManager',
            'hostInfo' => 'http://it-sochi',
            'baseUrl' => '/backend',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => require(__DIR__ . '/rules/backend-rules.php'),
        ],
    ],
];
