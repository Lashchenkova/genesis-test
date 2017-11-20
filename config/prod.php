<?php

use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use EXS\RabbitmqProvider\Providers\Services\RabbitmqProvider;

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

$app['rabbit.connections'] = [
    'default' => [
        'host' => 'localhost',
        'port' => 5672,
        'user' => 'root',
        'password' => 'root',
        'vhost' => '/'
    ]
];

$app['exs.rabbitmq.env'] = [
    'exchange' => '',
    'type' => 'fanout',
    'queue' => '',
    'key' => ''
];

$app['security.jwt'] = [
    'secret_key' => 'Very_secret_key',
    'life_time'  => 86400,
    'algorithm'  => ['HS256'],
    'options'    => [
        'header_name'  => 'Authorization',
        'token_prefix' => 'Bearer',
    ]
];

$app['security.firewalls'] = [
    'login' => [
        'pattern' => '^/|login|trackers|save',
        'logout' => ['logout_path' => '/logout'],
        'anonymous' => true,
    ],
    'secured' => [
        'pattern' => '^.*$',
        'logout' => ['logout_path' => '/logout'],
        'jwt' => [
            'use_forward' => true,
            'require_previous_session' => false,
            'stateless' => true,
        ]
    ],
];

$app->register(new Silex\Provider\SecurityServiceProvider());
$app->register(new Silex\Provider\SecurityJWTServiceProvider());

$app->register(new RabbitmqProvider());

$app->register(new TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/../resources/view',
]);

$app->register(new ServiceControllerServiceProvider());

$app->register(new DoctrineServiceProvider(), [
    'db.options' => $app['db.options']
]);

$app->register(new SessionServiceProvider());