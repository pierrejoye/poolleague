<?php
require __DIR__.'/../vendor/autoload.php';

use Pool\Application;

function check_or_create_json_dir(Application $app)
{
    if (is_dir($app->config('json_path')) === false) {
        mkdir($app->config('json_path'), 0777, true);
        mkdir($app->config('json_path').'users/github', 0777, true);
        mkdir($app->config('json_path').'extensions', 0777, true);
    }
}

$cacheDir = __DIR__.'/../cache/';
$app = new \Pool\Application(
    [
		'view'      => new \Pool\View\Twig(),
        //'view'      => new \Pool\View\Twig(['cache' => $cacheDir . 'twig']),
        'json_path' => __DIR__.'/json/',
        'cache_dir' => $cacheDir,
        'web_root_dir' => __DIR__,
    ]
);

// Redis client
$app->container->singleton(
    'redis.client',
    function (Set $container) {
        $config = $container->get('app.config');

        $client = new Predis\Client(sprintf('tcp://%s:%s', $config['redis']['host'], $config['redis']['port']));
        $client->select($config['redis']['db']);

        return $client;
    }
);

// User repository
$app->container->singleton(
    'user.repository',
    function (Set $container) {
        return new UserRepository($container->get('redis.client'));
    }
);


// Default
$app->get('/', 'Pool\Controller\DefaultController:indexAction');

// Authorization
$app->get('/login', 'Pool\Controller\AuthController:loginFormAction');
$app->post('/login', 'Pool\Controller\AuthController:loginAction');
$app->get('/logout', 'Pool\Controller\AuthController:logoutAction');


$app->run();
