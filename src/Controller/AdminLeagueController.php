<?php

namespace Pool\Controller;

use Pool\Entity\League;

/**
 * Class AdminLeagueController.
 */
class AdminLeagueController extends ControllerAbstract
{
    /**
     * GET /league/edit.
     */
    public function editFormAction($leagueId)
    {
        $leagueRepository = $this->app->container->get('league.repository');
        $league = $leagueRepository->find($leagueId);
        if (!$league) {
            $this->app->flash('error', 'Cannot find this league');
            $this->app->redirect('/league/list');

            return;
        }

        $this->app->render('admin/addLeague.html', [
            'league' => $league,
            'mode' => 'edit',
            'h' => $this->getHash(),
        ]
        );
    }

    /**
     * GET /league/edit.
     */
    public function editAction($leagueId)
    {
        $leagueRepository = $this->app->container->get('league.repository');
        $league = $leagueRepository->find($leagueId);
        if (!$league) {
            $this->app->flash('error', 'Cannot find this league');
            $this->app->redirect('/admin/league/list');

            return;
        }

        $this->getHash();
        $name = trim(filter_var($this->app->request()->post('name'), FILTER_SANITIZE_STRING));
        if (empty($name)) {
            $this->app->flash('error', 'Invalid name');
            $this->app->redirect('/league/edit/'.$leagueId);

            return;
        }
        $league->setName($name);
        $leagueRepository->persist($league);
        $this->app->redirect('/admin/league/list');
    }

    /**
     * POST /league/add.
     */
    public function addAction()
    {
        $this->getHash();
        $name = trim(filter_var($this->app->request()->post('name'), FILTER_SANITIZE_STRING));
        if (empty($name)) {
            $this->app->flash('error', 'Invalid name');
            $this->app->redirect('/admin/league/add');

            return;
        }

        $leagueRepository = $this->app->container->get('league.repository');

        $league = new League();
        $league->setName($name);
        $leagueRepository->persist($league);

        $this->app->redirect('/admin/league/list');
    }

    /**
     * GET /league/add.
     */
    public function addFormAction()
    {
        $this->app->render('admin/addLeague.html', [
            'mode' => 'add',
            'h' => $this->getHash(),
        ]
        );
    }

    /**
     * GET /admin/user/list/.
     */
    public function showAction($id)
    {
        $leagueRepository = $this->app->container->get('league.repository');
        $league = $leagueRepository->find($id);
        $this->app->render('admin/showLeague.html', [
            'league' => $league,
        ]);
    }

    /**
     * GET /admin/user/list/.
     */
    public function listAction()
    {
        $leagueRepository = $this->app->container->get('league.repository');
        $leagues = $leagueRepository->getAll();
        $this->app->render('admin/listLeague.html', [
            'leagues' => $leagues,
        ]);
    }
}

/* vim: set tabstop=4 shiftwidth=4 expandtab: fdm=marker */
