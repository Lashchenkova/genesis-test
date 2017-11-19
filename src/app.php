<?php

use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\RememberMeServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use App\Services\UserProvider;
use App\Services\UserService;
use App\Services\TrackerService;
use EXS\RabbitmqProvider\Providers\Services\RabbitmqProvider;
use EXS\RabbitmqProvider\Services\PostmanService;
use EXS\RabbitmqProvider\Services\ConsumerService;
use EXS\RabbitmqProvider\Services\AmqpService;

/** @var Application $app */
$app->before(function (Request $request) {
    if (!strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : []);
    }
});

$app->register(new RabbitmqProvider());

$app->register(new TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/../resources/view',
]);

$app->register(new ServiceControllerServiceProvider());

$app->register(new DoctrineServiceProvider(), [
    'db.options' => $app['db.options']
]);

$app->register(new SessionServiceProvider());

$app->register(new SecurityServiceProvider(), [
    'security.firewalls' => [
        'foo' => array('pattern' => '^/foo'), // Example of an url available as anonymous user
        'main' => [
            'pattern' => '^.*$',
            'anonymous' => true,
            'form' => [
                'login_path' => '/',
                'check_path' => '/login_check',
            ],
            'logout' => ['logout_path' => '/logout'],
            'users' =>function($app) {
                return new UserProvider($app['db']);
            },
        ]
    ],
//    'security.access_rules' => [
//        ['^/.+$', 'IS_AUTHENTICATED_FULLY'],
//        ['^/foo$', 'IS_AUTHENTICATED_ANONYMOUSLY'],
//    ]
]);

$app->register(new RememberMeServiceProvider());

$app->get('/', function(Request $request) use ($app)
{
    return $app['twig']->render('index.twig', [
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ]);
});

$userService = new UserService($app['db']);

$app->get('/search', function (Request $request) use ($app, $userService) {
    $query = $request->query->get('query');

    $users = $userService->search($query);

    return json_encode($users);
});

$app->post('/users', function (Request $request) use ($app, $userService) {
    $user = $request->request->all();

    $result = $userService->save($user);

    return json_encode($result);
});

$amqp = new AmqpService();
$postmanService = new PostmanService($amqp);
$consumerService = new ConsumerService($amqp);

$trackerService = new TrackerService($app['db'], $postmanService, $consumerService);

$app->post('/trackers', function (Request $request) use ($app, $trackerService) {
    $event = $request->request->all();

//    $result = $trackerService->publish($event);
//    $result = $trackerService->consume();

    return json_encode($event);
});

$app['rabbit.connections'] = array(
    'default' => array(
        'host' => 'localhost',
        'port' => 5672,
        'user' => 'root',
        'password' => 'root',
        'vhost' => '/'
    )
);

// rabbitmq provider environment
$app['exs.rabbitmq.env'] = array(
    'exchange' => 'REPLACE_EXCHANGE_NAME',
    'type' => 'REPLACE_EXCHANGE_TYPE',
    'queue' => 'REPLACE_QUEUE_NAME',
    'key' => 'REPLACE_ROUTING_KEY_NAME'
);

return $app;
