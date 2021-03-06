<?php

namespace Pool;

use Pool\Entity\User;
use Pool\Entity\UserRepository;
use RKA\Slim;

/**
 * Class Application.
 */
class Application extends Slim
{
    /**
     * @var callable
     */
    private $authentication;

    /**
     * @var array
     */
    private $innerCache = [];

    /**
     * @param array $userSettings
     */
    public function __construct(array $userSettings = array())
    {
        parent::__construct($userSettings);

        session_start();
        $this->authentication = function () {
            if ($this->user() === null) {
                $this->redirect('/login');

                return;
            }
        };
    }

    /**
     * @return User|null
     */
    public function user()
    {
        if (!isset($_SESSION['user'])) {
            return;
        };
		$user = null;
        if (!isset($this->innerCache['user'])) {
            /* @var $userRepository UserRepository */
            $em = $this->container->get('doctrine.entitymanager');
            $userRepository = $em->getRepository('Pool\Entity\User');
            $user = $userRepository->find($_SESSION['user']);

            if (is_null($user)) {
                return;
            }

            $this->innerCache['user'] = $user;
        }
        $route = $this->router()->getCurrentRoute();

        if ($route) {
            $routePattern = $route->getPattern();
            if (!is_null($user)) {
                if (substr($routePattern, 0, 6) == '/admin' && !$user->isAdmin()) {
                    $this->flash('error', 'Not allowed to access this section, brought back to home');
                    //$this->redirect('/');
                }
            }
        }

        return $this->innerCache['user'];
    }

    /**
     * @param mixed    $condition
     * @param string   $url
     * @param int|null $status
     *
     * @return $this
     */
    public function redirectIf($condition, $url, $status = null)
    {
        if ((bool) $condition) {
            $this->redirect($url, $status ?: 302);
        }

        return $this;
    }

    /**
     * @param mixed    $condition
     * @param string   $url
     * @param int|null $status
     *
     * @return $this
     */
    public function redirectUnless($condition, $url, $status = null)
    {
        if ((bool) $condition === false) {
            $this->redirect($url, $status ?: 302);
        }

        return $this;
    }

    /**
     * @param mixed $condition
     *
     * @return $this
     */
    public function notFoundIf($condition)
    {
        if ((bool) $condition === true) {
            $this->notFound();
        }

        return $this;
    }

    /**
     * @param int $code
     *
     * @return $this
     */
    public function renderError($code)
    {
        $this->render('errors/'.$code.'.html');
        $this->response()->status($code);
        $this->stop();

        return $this;
    }

    /**
     * @param string $template
     * @param array  $data
     * @param null   $status
     */
    public function render($template, $data = array(), $status = null)
    {
        // add user in template data
        $data = array_merge(
            [
                'user' => $this->user(),
            ],
            $data ?: []
        );

        parent::render($template, $data, $status);
    }

    /**
     * @param callable $callback
     *
     * @return $this
     */
    public function then(callable $callback)
    {
        $callback($this);

        return $this;
    }

    /**
     * @param callable $callback
     *
     * @return $this
     */
    public function otherwise(callable $callback)
    {
        return $this->then($callback);
    }

    /**
     * Run application.
     */
    public function run()
    {
        $this->error(
            function () {
                $this->renderError(500);
            }
        );

        $this->notFound(
            function () {
                $this->renderError(404);
            }
        );

        parent::run();
    }

    /**
     * @param string          $route
     * @param callable|string $callable
     *
     * @return \Slim\Route
     */
    public function getSecured($route, $callable)
    {
        return $this->get($route, $this->authentication, $callable);
    }

    /**
     * @param string          $route
     * @param callable|string $callable
     *
     * @return \Slim\Route
     */
    public function postSecured($route, $callable)
    {
        return $this->post($route, $this->authentication, $callable);
    }

    /**
     * @param string $body
     * @param int    $code
     *
     * @return \Slim\Route
     */
    public function jsonResponse($body, $code)
    {
        $response = $this->response();
        $response['Content-Type'] = 'application/json';
        $response->status($code);
        $response->body(json_encode($body));

        return $this;
    }
}

/* vim: set tabstop=4 shiftwidth=4 expandtab: fdm=marker */
