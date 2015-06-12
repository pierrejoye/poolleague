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
    public function editFormAction($id)
    {
        $em = $this->app->container->get('doctrine.entitymanager');
        $league = $em->getRepository('Pool\Entity\League')->find($id);
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
    public function editAction($id)
    {
        $em = $this->app->container->get('doctrine.entitymanager');
        $league = $em->getRepository('Pool\Entity\League')->find($id);
        if (!$league) {
            $this->app->flash('error', 'Cannot find this league');
            $this->app->redirect('/admin/league/list');

            return;
        }

        $this->getHash();
        $name = trim(filter_var($this->app->request()->post('name'), FILTER_SANITIZE_STRING));
        if (empty($name)) {
            $this->app->flash('error', 'Invalid name');
            $this->app->redirect('/league/edit/'.$id);

            return;
        }
        $league->setName($name);
        $em->persist($league);
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

        $league = new League();
        $league->setName($name);

        $em = $this->app->container->get('doctrine.entitymanager');
        $em->persist($league);
        $em->flush();

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
        $em = $this->app->container->get('doctrine.entitymanager');
        $league = $em->getRepository('Pool\Entity\League')->find($id);

        $tournaments = $em->getRepository('Pool\Entity\Tournament')->findBy(['league' => $league->getId()]);
        $leagueTeams = $league->getTeams();

        $this->app->render('admin/showLeague.html', [
            'league' => $league,
            'teams' => $leagueTeams,
            'tournaments' => $tournaments,
        ]);
    }

    /**
     * GET /admin/user/list/.
     */
    public function listAction()
    {
        $em = $this->app->container->get('doctrine.entitymanager');
        $leagues = $em->getRepository('Pool\Entity\League')->findAll();
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

        $em = $this->app->container->get('doctrine.entitymanager');
        $league = $em->getRepository('Pool\Entity\League')->find($leagueId);
        if (!$league) {
            $this->app->flash('error', 'Cannot find this league');
            $this->app->redirect('/admin/league/list');

            return;
        }

        $teamsLeague = $league->getTeams();
        $teamsAll = $em->getRepository('Pool\Entity\Team')->findAll();

        if (!is_array($teamsAll)) {
            $this->app->flash('error', 'Please first create teams');
            $this->app->redirect('/admin/league/list');

            return;
        }

        $listNotInLeague = [];
        foreach ($teamsAll as $team) {
            $listNotInLeague[$team->getId()] = $team->getName();
        }
        $listInLeague = [];
        foreach ($teamsLeague as $team) {
            $listInLeague[$team->getId()] = $listNotInLeague[$team->getId()];
        }
        $listNotInLeague = array_diff($listNotInLeague, $listInLeague);

        $this->app->render('admin/leagueAddTeam.html', [
            'h' => $this->getHash(),
            'league' => $league,
            'listNotInLeague' => $listNotInLeague,
            'listInLeague' => $listInLeague,
        ]);
    }

    /**
     * POST /admin/league/:id/team/edit.
     */
    public function editTeamAction($leagueId)
    {
        $this->getHash();
        $em = $this->app->container->get('doctrine.entitymanager');
        $leagueRepository = $em->getRepository('Pool\Entity\League');
        $league = $leagueRepository->find($leagueId);
        if (!$league) {
            $this->app->flash('error', 'Cannot find this league');
            $this->app->redirect('/admin/league/list');

            return;
        }

        $teamsId = $this->app->request()->post('teams');

        if (!is_array($teamsId) || count($teamsId) < 2) {
            $this->app->flash('error', 'At least two teams must be selected (or created)');
            $this->app->redirect('/admin/league/list');

            return;
        }
        foreach ($teamsId as $teamId) {
            if ((int) $teamId < 1) {
                $this->app->flash('error', 'Invalid team');
                $this->app->redirect('/admin/league/list');
            }
        }

        $teams = $em->getRepository('Pool\Entity\Team')->findBy(['id' => $teamsId]);
        if (count($teams) != count($teamsId)) {
            $this->app->flash('error', 'Invalid team');
            $this->app->redirect('/admin/league/list');
        }

        $teamsInLeagueObject = $league->getTeams();
        $teamsInLeagueNames = [];
        foreach ($teamsInLeagueObject as $team) {
            $league->removeTeam($team);
        }
        $em->persist($league);
        $em->flush();

        foreach ($teams as $team) {
            $league->addTeam($team);
        }
        $teams = $league->getTeams();

        $em->persist($league);
        $em->flush();
        $this->app->redirect('/admin/league/'.$league->getId().'/show');
    }
}

/* vim: set tabstop=4 shiftwidth=4 expandtab: fdm=marker */
