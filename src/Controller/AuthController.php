<?php

namespace Pool\Controller;

//use Pool\Auth\ProviderInterface;
use Pool\Entity\User;

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
        $this->app->render('login.html', ['h' => $this->getHash()]);
    }

    /**
     */
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
        $this->checkHash();
        $email = $this->app->request()->post('email');
        $password = $this->app->request()->post('password');
        if (empty($email) || empty($password) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->invalidLogin();

            return;
        }

        $userRepository = $this->app->container->get('user.repository');
        $user = $userRepository->findByEmail($email);
        if (!$user) {
            die('no user found');
            $this->invalidLogin();

            return;
        }

        $user->checkPassword($password);
        $_SESSION['user'] = $user->getId();
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
