<?php

namespace Pool\Controller;

use Pool\Application;

/**
 * Class ControllerAbstract.
 */
abstract class ControllerAbstract
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @param Application $app
     */
    public function setApp(Application $app)
    {
        $this->app = $app;
    }

    protected function getHash()
    {
        $h = bin2hex(openssl_random_pseudo_bytes(16));
        $_SESSION['h'] = $h;
        return $h;
    }

    protected function checkHash()
    {
		$hRequest = $this->app->request()->post('h');
		$hSession = isset($_SESSION['h']) ?  $_SESSION['h'] : false;
		if (!is_string($hRequest) || !is_string($hSession)) {
			$failed = true;
		} else {
			if (!hash_equals($hSession, $hRequest)) {
				$failed = true;
			}
		}

		unset($_SESSION['h']);

		if ($failed) {
			$this->app->redirect('/');
		}
    }
}

/* vim: set tabstop=4 shiftwidth=4 expandtab: fdm=marker */
