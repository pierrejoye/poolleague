<?php

namespace Pool\Controller;

//use Pool\Auth\ProviderInterface;
use Pool\Entity\User;
use Pool\Entity\UserRepository;

/**
 * Class AuthController.
 */
class AuthController extends ControllerAbstract
{
    /**
     * GET /login.
     */
    public function loginFormAction()
    {
		$h = bin2hex(openssl_random_pseudo_bytes(16));
		$_SESSION['h'] = $h;
        $this->app->render('login.html', [ 'h' => $h ]);
    }

	protected function invalidLogin()
	{
		$this->app->flash('error', 'Invalid password or email');
		$this->app->redirect('/login');
	}

    /**
     * GET /login.
     */
    public function loginAction()
    {
		if (!hash_equals($this->app->request()->post('h'),  $_SESSION['h'])) {
			$this->app->redirect('/');
			return;
		}

		$email    = $this->app->request()->post('email');
		$password = $this->app->request()->post('password');
		if (empty($email) || empty($password) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$this->invalidLogin();
			return;
		}

		$userRepository = $this->app->container->get('user.repository');
		$user = $userRepository->find($email);
		if (!$user) {
			$this->invalidLogin();
			return;
		}
		
		$_SESSION['email'] = $user->getId();
        $this->app->redirect('/');
    }

    /**
     * GET /logout.
     */
    public function logoutAction()
    {
        session_destroy();

        $this->app->redirect('/');
    }
}

/* vim: set tabstop=4 shiftwidth=4 expandtab: fdm=marker */
