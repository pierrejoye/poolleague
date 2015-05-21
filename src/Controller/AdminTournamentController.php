<?php

namespace Pool\Controller;

use Pool\Entity\League;
use Pool\Entity\Tournament;

/**
 * Class AdminTournamentController.
 */
class AdminTournamentController extends ControllerAbstract
{
    protected function findLeagueById($leagueId)
    {
        $leagueRepository = $this->app->container->get('league.repository');
        $league = $leagueRepository->find($leagueId);
        if (!$league) {
            $this->app->flash('error', 'Cannot find this league');
            $this->app->redirect('/admin/league/'.$leagueId.'/list');
            return;
        }
        return $league;
    }

    /**
     * GET /admmin/tournament/edit.
     */
    public function editFormAction($tournamentId)
    {
        $leagueRepository = $this->app->container->get('tournament.repository');
        $tournament = $tournamentRepository->find($tournamentId);
        if (!$tournament) {
            $this->app->flash('error', 'Cannot find this tournament');
            $this->app->redirect('/admin/league/'.$league->getId().'/list');
            return;
        }

        $this->app->render('admin/addTournament.html', [
            'league' => $tournament,
            'mode' => 'edit',
            'h' => $this->getHash(),
        ]
        );
    }

    /**
     * POST /admin/tournament/edit.
     */
    public function editAction($tournamentId)
    {
        $leagueRepository = $this->app->container->get('league.repository');
        $league = $leagueRepository->find($leagueId);
        if (!$league) {
            $this->app->flash('error', 'Cannot find this league');
            $this->app->redirect('/admin/league/'.$league->getId().'/list');
            return;
        }

        $this->getHash();
        $name = trim(filter_var($this->app->request()->post('name'), FILTER_SANITIZE_STRING));
        if (empty($name)) {
            $this->app->flash('error', 'Invalid name');
            $this->app->redirect('/admin/league/'.$leagueId.'/tournament/edit/'.$tournamentId);
            return;
        }
        $league->setName($name);
        $leagueRepository->persist($league);
        $this->app->redirect('/admin/league/'.$league->getId().'/list');
    }

    /**
     * GET /admin/tournament/add.
     */
    public function addFormAction($leagueId)
    {
        $league = $this->findLeagueById($leagueId);
        $this->app->render('admin/addTournament.html',
            [
                'mode'   => 'add',
                'league' => $league,
                'h'      => $this->getHash(),
            ]
        );
    }

    /**
     * POST /admin/tournament/add.
     */
    public function addAction($leagueId)
    {
        $this->getHash();
        $league = $this->findLeagueById($leagueId);
        $name = trim(filter_var($this->app->request()->post('name'), FILTER_SANITIZE_STRING));
        $datePost = trim($this->app->request()->post('tournamentDate'));
        $format = 'd/m/Y';
        $tz = new \DateTimezone('Asia/Bangkok');
        $date = \DateTime::createFromFormat($format, $datePost, $tz);
        if (!$date) {
            $this->app->flash('error', 'Invalid date');
            $this->app->redirect('/admin/league/'.$league->getId().'/tournament/add');
        }

        $tournamentDate = $date->format($format);
        if ($tournamentDate != $datePost) {
            $this->app->flash('error', 'Invalid date');
            $this->app->redirect('/admin/league/'.$league->getId().'/tournament/add');
        }

        $tournament = new Tournament;
        $tournament->setDate($tournamentDate);
        $tournament->setName($name);

        $league->addTournament($tournament);

        $leagueRepository = $this->app->container->get('league.repository')->persist($league);
        $this->app->redirect('/admin/league/'.$league->getId().'/show');
    }

    /**
     * GET /admin/tournament/list.
     */
    public function showAction($id)
    {
        $league = $this->findLeagueById($leagueId);

        $this->app->render('admin/showLeague.html', [
            'league' => $league,
            'tournaments' => $tournaments
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
