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
        $em = $this->app->container->get('doctrine.entitymanager');
        $user = $em->getRepository('Pool\Entity\User')->findOneBy(['email' => $email]);

        if (!$user) {
            $this->invalidLogin();

            return;
        }
        if (!$user->checkPassword($password)) {
            $this->invalidLogin();
        }
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
