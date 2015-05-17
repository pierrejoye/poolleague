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
        $this->app->render('login.html',
        [
        'h' => $h
        ]);
    }

    /**
     * GET /login.
     */
    public function loginAction()
    {
		$h = $_SESSION['h'];
		unset($_SESSION['h']);
		if ($this->app->request()->post('h') != $h) {
			$this->app->redirect('/');
			return;
		}

		$email    = $this->app->request()->post('email');
		$password = $this->app->request()->post('password');
		if (empty($email) || empty($password) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$this->app->flash('error', 'Invalid password or email');
			$this->app->redirect('/login');
			return;
		}
		$_SESSION['email'] = $email;
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
