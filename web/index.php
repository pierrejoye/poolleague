<?php
require __DIR__.'/../vendor/autoload.php';

function check_or_create_json_dir(\Pool\Application $app)
{
    if (is_dir($app->config('json_path')) === false) {
        mkdir($app->config('json_path'), 0777, true);
        mkdir($app->config('json_path').'users/github', 0777, true);
        mkdir($app->config('json_path').'extensions', 0777, true);
    }
}

function check_or_create_json_dir(\Pool\Application $app)
{
    if (is_dir($app->config('json_path')) === false) {
        mkdir($app->config('json_path'), 0777, true);
        mkdir($app->config('json_path').'users/github', 0777, true);
        mkdir($app->config('json_path').'extensions', 0777, true);
    }
}

$cacheDir = __DIR__.'/../cache-dir/';
$app = new \Pool\Application(
    [
		'view'      => new \Pool\View\Twig(),
        //'view'      => new \Pool\View\Twig(['cache' => $cacheDir . 'twig']),
        'json_path' => __DIR__.'/json/',
        'cache_dir' => $cacheDir,
        'web_root_dir' => __DIR__,
    ]
);

$app->get('/hello/:name', function ($name) {
    echo "Hello, " . $name;
});
$app->run();
