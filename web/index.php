<?php
require __DIR__.'/../vendor/autoload.php';
use Slim\Helper\Set;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;  
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;


use Pool\Application;
use Pool\Entity\UserRepository;
use Pool\Entity\TeamRepository;
use Pool\Entity\LeagueRepository;
use Pool\Entity\TournamentRepository;
use Pool\Entity\RoundRepository;
use Pool\Entity\League;

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
		//'view'      => new \Pool\View\Twig(['strict_variables' => true]),
        //'view'      => new \Pool\View\Twig(['cache' => $cacheDir . 'twig']),
        'json_path' => __DIR__.'/json/',
        'cache_dir' => $cacheDir,
        'web_root_dir' => __DIR__,
    ]
);

// Config
$app->container->singleton(
    'app.config',
    function (Set $container) {
        return json_decode(file_get_contents(__DIR__.'/../src/config.json'), true);
    }
);

// Config
$app->container->singleton(
    'doctrine.entitymanager',
    function (Set $container) {
		$paths = [
			__DIR__."/../src/Entity/",
		];

		$isDevMode = true;  
		$dbParams = $container->get('app.config')['doctrine'];
		$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, null, null, FALSE);
		return EntityManager::create($dbParams, $config);
    }
);


// PDO client
$app->container->singleton(
    'pdo.client',
    function (Set $container) {
		$config = $container->get('app.config');
		$dsn = 'mysql:host='.$config['pdo']['host'].';dbname='.$config['pdo']['database'];
		$pdo = new \PDO($dsn, $config['pdo']['user'], $config['pdo']['password']);
		$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }
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

if (0) {
// User repository
$app->container->singleton(
    'user.repository',
    function (Set $container) {
        return new UserRepository($container->get('pdo.client'));
    }
);

// Team repository
$app->container->singleton(
    'team.repository',
    function (Set $container) {
        return new TeamRepository($container->get('pdo.client'));
    }
);

// League repository
$app->container->singleton(
    'league.repository',
    function (Set $container) {
        return new LeagueRepository($container->get('pdo.client'));
    }
);


// Tournament repository
$app->container->singleton(
    'tournament.repository',
    function (Set $container) {
        return new TournamentRepository($container->get('pdo.client'));
    }
);

// Tournament repository
$app->container->singleton(
    'round.repository',
    function (Set $container) {
        return new RoundRepository($container->get('pdo.client'));
    }
);
}
// Default
$app->get('/', 'Pool\Controller\DefaultController:indexAction');

// User
$app->getSecured('/profile', 'Pool\Controller\UserController:profileAction');


// Authorization
$app->get('/login', 'Pool\Controller\AuthController:loginFormAction');
$app->post('/login', 'Pool\Controller\AuthController:loginAction');
$app->getSecured('/logout', 'Pool\Controller\AuthController:logoutAction');

// Admin

// User
$app->getSecured('/admin/user/list', 'Pool\Controller\AdminUserController:listAction');

$app->getSecured('/admin/user/add', 'Pool\Controller\AdminUserController:addUserFormAction');
$app->getSecured('/admin/user/del/:id', 'Pool\Controller\AdminUserController:removeUserAction');
$app->postSecured('/admin/user/add', 'Pool\Controller\AdminUserController:addUserAction');

$app->getSecured('/admin/user/edit/:id', 'Pool\Controller\AdminUserController:editFormAction');
$app->postSecured('/admin/user/edit/:id', 'Pool\Controller\AdminUserController:updateAction');

$app->getSecured('/admin/team/list', 'Pool\Controller\AdminTeamController:listAction');

// Team
$app->getSecured('/team/:id/player/list', 'Pool\Controller\TeamController:playerList');

$app->getSecured('/admin/team/add', 'Pool\Controller\AdminTeamController:addFormAction');
$app->postSecured('/admin/team/add', 'Pool\Controller\AdminTeamController:addAction');

$app->getSecured('/team/:id/player/add', 'Pool\Controller\TeamController:playerAddForm');
$app->postSecured('/team/:id/player/add', 'Pool\Controller\TeamController:playerAdd');

$app->getSecured('/admin/team/edit/:id', 'Pool\Controller\AdminTeamController:editFormAction');
$app->postSecured('/admin/team/edit/:id', 'Pool\Controller\AdminTeamController:updateAction');


// League 
$app->get('/admin/league/list', 'Pool\Controller\AdminLeagueController:listAction');

$app->getSecured('/admin/league/:id/show', 'Pool\Controller\AdminLeagueController:showAction');

$app->getSecured('/admin/league/add', 'Pool\Controller\AdminLeagueController:addFormAction');
$app->postSecured('/admin/league/add', 'Pool\Controller\AdminLeagueController:addAction');

$app->getSecured('/admin/league/:id/edit', 'Pool\Controller\AdminLeagueController:editFormAction');
$app->postSecured('/admin/league/:id/edit', 'Pool\Controller\AdminLeagueController:editAction');

$app->getSecured('/admin/league/:id/tournament/add', 'Pool\Controller\AdminTournamentController:addFormAction');
$app->postSecured('/admin/league/:id/tournament/add', 'Pool\Controller\AdminTournamentController:addAction');

$app->getSecured('/admin/tournament/:tournamentid/edit', 'Pool\Controller\AdminTournamentController:editFormAction');
$app->postSecured('/admin/tournament/:tournamentid/edit', 'Pool\Controller\AdminTournamentController:editAction');
$app->getSecured('/admin/tournament/:tournamentid/show', 'Pool\Controller\AdminTournamentController:showAction');

$app->getSecured('/admin/league/:id/team/edit', 'Pool\Controller\AdminLeagueController:editTeamFormAction');
$app->postSecured('/admin/league/:id/team/edit', 'Pool\Controller\AdminLeagueController:editTeamAction');

// Score
$app->getSecured('/admin/score/:scoreId/edit', 'Pool\Controller\AdminScoreController:editFormShow');
$app->postSecured('/admin/score/:scoreId/edit', 'Pool\Controller\AdminScoreController:editFormAction');
$app->run();
