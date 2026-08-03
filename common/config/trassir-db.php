<?php

use yii\db\Connection;

return [
    'class' => Connection::class,
    'dsn' => 'pgsql:host=192.168.124.5;port=5432;dbname=postgres',
    'username' => 'yii_trassir',
    'password' => 'Adm1BDM!',
    'charset' => 'utf8',
    'enableSchemaCache' => false,
];
