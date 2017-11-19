<?php

$app['api.endpoint'] = '/api';

/**
 * MySQL
 */
$app['db.options'] = [
    'driver' => 'pdo_mysql',
    'user' => 'root',
    'password' => 'root',
    'dbname' => 'genesis-test',
    'host' => 'mysql',
];
