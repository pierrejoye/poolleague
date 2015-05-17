<?php

namespace Pool\Controller;


/**
 * Class DefaultController.
 */
class DefaultController extends ControllerAbstract
{

    /**
     * GET /.
     */
    public function indexAction()
    {

        $this->app->render('home.html',
            [

            ]);
    }
}

/* vim: set tabstop=4 shiftwidth=4 expandtab: fdm=marker */
