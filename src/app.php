<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use App\Services\UserProvider;
use App\Services\UserService;
use App\Services\TrackerService;
use EXS\RabbitmqProvider\Services\PostmanService;
use EXS\RabbitmqProvider\Services\ConsumerService;
use EXS\RabbitmqProvider\Services\AmqpService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\User;
use Silex\Component\Security\Http\Token\JWTToken;

/** @var Application $app */
$app->before(function (Request $request) {
    if (!strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : []);
    }
});

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

$amqp = new AmqpService($app['rabbit.connections']['default'], $app['exs.rabbitmq.env']);

$postmanService = new PostmanService($amqp);
$consumerService = new ConsumerService($amqp);

$trackerService = new TrackerService($app['db'], $postmanService, $consumerService);

$app->post('/trackers', function (Request $request) use ($app, $trackerService, $amqp) {
    $event = $request->request->all();
//    $amqp->amqpConnect();

    $trackerService->publish($event);
//    $result = $trackerService->consume();

    return json_encode(true);
});

$app['users'] = new UserProvider($app['db']);

$app['security.firewalls']['secured']['users'] = $app['users'];

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

    if (!$token instanceof JWTToken) {
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
