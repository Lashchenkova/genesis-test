<?php

use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use App\Services\UserProvider;
use App\Services\UserService;
use App\Services\TrackerService;
use EXS\RabbitmqProvider\Providers\Services\RabbitmqProvider;
use EXS\RabbitmqProvider\Services\PostmanService;
use EXS\RabbitmqProvider\Services\ConsumerService;
use EXS\RabbitmqProvider\Services\AmqpService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\User;

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

$app->get('/', function() use ($app)
{
    return $app['twig']->render('index.twig');
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
$app['rabbit.connections'] = [
    'default' => [
        'host' => 'localhost',
        'port' => 5672,
        'user' => 'root',
        'password' => 'root',
        'vhost' => '/'
    ]
];

// rabbitmq provider environment
$app['exs.rabbitmq.env'] = [
    'exchange' => '',
    'type' => 'fanout',
    'queue' => '',
    'key' => ''
];

$amqp = new AmqpService($app['rabbit.connections']['default'], $app['exs.rabbitmq.env']);

$postmanService = new PostmanService($amqp);
$consumerService = new ConsumerService($amqp);

$trackerService = new TrackerService($app['db'], $postmanService, $consumerService);

$app->post('/trackers', function (Request $request) use ($app, $trackerService, $amqp) {
    $event = $request->request->all();

    $trackerService->publish($event);
//    $result = $trackerService->consume();

    return json_encode(true);
});

$app['security.jwt'] = [
    'secret_key' => 'Very_secret_key',
    'life_time'  => 86400,
    'algorithm'  => ['HS256'],
    'options'    => [
        'header_name'  => 'Authorization',
        'token_prefix' => 'Bearer',
    ]
];
$app['users'] = new UserProvider($app['db']);

$app['security.firewalls'] = [
    'login' => [
        'pattern' => '^/|login|trackers|save',
        'logout' => ['logout_path' => '/logout'],
        'anonymous' => true,
    ],
    'secured' => [
        'pattern' => '^.*$',
        'logout' => ['logout_path' => '/logout'],
        'users' => $app['users'],
        'jwt' => [
            'use_forward' => true,
            'require_previous_session' => false,
            'stateless' => true,
        ]
    ],
];

$app->register(new Silex\Provider\SecurityServiceProvider());
$app->register(new Silex\Provider\SecurityJWTServiceProvider());

//Authorization
$app->post('/login', function(Request $request) use ($app, $userService){
    $vars = json_decode($request->getContent(), true);

    try {
        if (empty($vars['_username']) || empty($vars['_password'])) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $vars['_username']));
        }

        /** @var $user User */
        $user = $app['users']->loadUserByUsername($vars['_username']);

        if (! $app['security.encoder.digest']->isPasswordValid($user->getPassword(), $vars['_password'], '')) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $vars['_username']));
        } else {
            $id = $userService->getUserId($user->getUsername());
            $response = [
                'success' => true,
                'token' => $app['security.jwt.encoder']->encode(['name' => $user->getUsername()]),
                'id' => $id
            ];
        }
    } catch (UsernameNotFoundException $e) {
        $response = [
            'success' => false,
            'error' => 'Invalid credentials',
        ];
    }
    return $app->json($response, ($response['success'] == true ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST));
});

$app->get('/profiles/own', function() use ($app, $userService){
    $jwt = 'no';
    $token = $app['security.token_storage']->getToken();

    if (!$token instanceof Silex\Component\Security\Http\Token\JWTToken) {
        return $app->json([
            'auth' => $jwt
        ]);
    }

    $jwt = 'yes';
    $username = $token->getUser()->getUsername();
    $id = $userService->getUserId($username);

    return $app->json([
        'hello' => $token->getUsername(),
        'username' => $username,
        'auth' => $jwt,
        'user_id' => $id
    ]);
});

return $app;
