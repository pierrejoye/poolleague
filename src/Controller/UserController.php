<?php

namespace Pool\Controller;

use Pool\Entity\User;
use Pool\Entity\UserRepository;

/**
 * Class UserController.
 */
class UserController extends ControllerAbstract
{
    /**
     * GET /profile.
     */
    public function profileAction()
    {
        $this->app->render('user/profile.html', [
        'user' => $this->app->user()
        ]);
    }
}

/* vim: set tabstop=4 shiftwidth=4 expandtab: fdm=marker */
