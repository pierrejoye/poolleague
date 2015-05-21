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
        $teamsId = $league->getTeams();

        $teamRepository = $this->app->container->get('team.repository');
        foreach ($teamsId as $teamId) {
            $toAdd = $teamRepository->find($teamId);
            $teams[] = $toAdd;
        }

        $this->app->render('admin/showLeague.html', [
            'league' => $league,
            'teams' => $teams,
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

    /**
     * GET /admin/league/:id/team/edit.
     */
    public function editTeamFormAction($leagueId)
    {
        $this->getHash();

        $leagueRepository = $this->app->container->get('league.repository');
        $league = $leagueRepository->find($leagueId);
        if (!$league) {
            $this->app->flash('error', 'Cannot find this league');
            $this->app->redirect('/admin/league/list');

            return;
        }

        $teamsLeague = $league->getTeams();
        $teamRepository = $this->app->container->get('team.repository');
        $teamsAll = $teamRepository->getAll();

        if (!is_array($teamsAll)) {
            $this->app->flash('error', 'Please first create teams');
            $this->app->redirect('/admin/league/list');

            return;
        }
        foreach ($teamsAll as $team) {
            $listSelect[$team->getId()] = [
                'name' => $team->getName(),
                'active' => false,
            ];
        }

        foreach ($teamsLeague as $team) {
            $listSelect[$team->id]['active'] = true;
        }

        $leagueTeams = $league->getTeams();

        $this->app->render('admin/leagueAddTeam.html', [
            'h' => $this->getHash(),
            'league' => $league,
            'listSelectTeams' => $listSelect,
        ]);
    }

    /**
     * POST /admin/league/:id/team/edit.
     */
    public function editTeamAction($leagueId)
    {
        $this->getHash();

        $leagueRepository = $this->app->container->get('league.repository');
        $league = $leagueRepository->find($leagueId);
        if (!$league) {
            $this->app->flash('error', 'Cannot find this league');
            $this->app->redirect('/admin/league/list');

            return;
        }
        $teams = $this->app->request()->post('teams');

        if (!is_array($teams) || count($teams) < 2) {
            $this->app->flash('error', 'At least two teams must be selected (or created)');
            $this->app->redirect('/admin/league/list');

            return;
        }
        $teamsElem = count($teams);

        for ($i = 0; $i < $teamsElem; $i++) {
            $teams[$i] = (int) $teams[$i];
        }

        $league->setTeamsFromId($teams);
        $leagueRepository->persist($league);

        $this->app->redirect('/admin/league/'.$league->getId().'/show');
    }
}

/* vim: set tabstop=4 shiftwidth=4 expandtab: fdm=marker */
