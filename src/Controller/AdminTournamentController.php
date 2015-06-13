<?php

namespace Pool\Controller;

use Pool\Entity\League;
use Pool\Entity\Tournament;
use Pool\Entity\Round;
use Pool\Entity\Game;
use Pool\Util\Fixture;

/**
 * Class AdminTournamentController.
 */
class AdminTournamentController extends ControllerAbstract
{
    protected function findLeagueById($leagueId)
    {
        $em = $this->app->container->get('doctrine.entitymanager');
        $league = $em->getRepository('Pool\Entity\League')->find($leagueId);

        if (!$league) {
            $this->app->flash('error', 'Cannot find this league');
            $this->app->redirect('/admin/league/'.$leagueId.'/list');

            return;
        }

        return $league;
    }

    protected function validDate($datePost)
    {
        $format = 'j/n/Y';
        $tz = new \DateTimezone('Asia/Bangkok');
        $date = \DateTime::createFromFormat($format, $datePost, $tz);

        if (!$date) {
            return false;
        }

        $dateStr = $date->format($format);

        if ($dateStr != $datePost) {
            return false;
        }

        return $date;
    }

    /**
     * GET /admmin/tournament/edit.
     */
    public function editFormAction($tournamentId)
    {
        $tournament = $this->app->container->get('tournament.repository')->find($tournamentId);
        $league = $this->app->container->get('league.repository')->find($tournament->getLeagueId());
        if (!$tournament) {
            $this->app->flash('error', 'Cannot find this tournament');
            $this->app->redirect('/admin/league/'.$league->getId().'/list');

            return;
        }
        if (!$league) {
            $this->app->flash('error', 'Cannot find the league for this tournament');
            $this->app->redirect('/admin/league/'.$league->getId().'/list');

            return;
        }

        $this->app->render('admin/addTournament.html', [
            'tournament' => $tournament,
            'league' => $league,
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
        $this->getHash();

        $tournamentRepository = $this->app->container->get('tournament.repository');
        $leagueRepository = $this->app->container->get('league.repository');
        $tournament = $tournamentRepository->find($tournamentId);
        if (!$tournament) {
            $this->app->flash('error', 'Cannot find this tournament');
            $this->app->redirect('/admin/league/'.$league->getId().'/list');

            return;
        }

        $league = $leagueRepository->find($tournament->getLeagueId());
        if (!$league) {
            $this->app->flash('error', 'cannot find the league for this tournament');
            echo 'not found';
            $this->app->redirect('/admin/league/'.$league->getId().'/list');

            return;
        }

        $name = trim(filter_var($this->app->request()->post('name'), FILTER_SANITIZE_STRING));
        $date = trim(filter_var($this->app->request()->post('tournamentStartDate'), FILTER_SANITIZE_STRING));
        $weekday = filter_var($this->app->request()->post('weekday'), FILTER_VALIDATE_INT, ['min_range' => 1, 'max_range' => 7]);
        $tournamentIdForm = filter_var($this->app->request()->post('id'), FILTER_VALIDATE_INT, ['min_range' => 1]);
        $rounds = filter_var($this->app->request()->post('rounds'), FILTER_VALIDATE_INT, ['min_range' => 1]);
        $leagueId = filter_var($this->app->request()->post('leagueId'), FILTER_VALIDATE_INT, ['min_range' => 1]);

        if (!$tournamentId || $tournamentId != $tournamentIdForm) {
            $msg[] = 'Invalid input, id error';
        }

        if (empty($name)) {
            $msg[] = 'Invalid name';
        }

        $tournamentDate = $this->validDate($date);
        if (!$tournamentDate) {
            $msg[] = 'Invalid date';
        }

        if (!$weekday) {
            $msg[] = 'Invalid weekday';
        }

        if (!$rounds) {
            $msg[] = 'Invalid rounds number';
        }

        if (count($msg) > 0) {
            $this->app->flash('error', implode('<br />', $msg));
            $this->app->redirect('/admin/tournament/'.$tournamentId.'/edit');
        }

        $tournament->setRounds($rounds);
        $tournament->setDate($tournamentDate);
        $tournament->setWeekday($weekday);
        $tournament->setLeagueId($league->getId());
        $tournament->setName($name);

        $tournamentRepository->persist($tournament);

        $this->app->redirect('/admin/tournament/'.$tournament->getId().'/show');
    }

    /**
     * GET /admin/tournament/add.
     */
    public function addFormAction($leagueId)
    {
        $league = $this->findLeagueById($leagueId);
        $teams = $league->getTeams();

        $em = $this->app->container->get('doctrine.entitymanager');
        $teamRepository = $em->getRepository('Pool\Entity\Team');

        $teamsAll = $teamRepository->findAll();

        $fixture = Fixture::factory();
        $teamsId = [];
        $i = 1;
        foreach ($teams as $team) {
            $teamsId[$i++] = $team->getId();
        }

        $fixture->setData($teamsId);
        $fixture->generate();
        $data = $fixture->getRounds();

        $teamBye = new \Pool\Entity\Team();
        $teamBye->setName('Bye');
        $teams[] = $teamBye;
        $teamFixture = [];
        foreach ($data as $round) {
            $toShow = [];
            foreach ($round as $match) {
                $homeId = $teamsId[$match['home']];
                $awayId = $teamsId[$match['away']];

                if ($homeId) {
                    $teamHome = $teamRepository->find($homeId);
                } else {
                    $teamHome = $teamBye;
                }
                if ($awayId) {
                    $teamAway = $teamRepository->find($awayId);
                } else {
                    $teamAway = $teamBye;
                }
                $toShow[] = [
                    'home' => $teamHome,
                    'visitor' => $teamAway,
                ];
            }
            $teamFixture[] = $toShow;
        }

        $this->app->render('admin/addTournament.html',
            [
                'mode' => 'add',
                'league' => $league,
                'teams' => $teams,
                'fixtures' => $teamFixture,
                'h' => $this->getHash(),
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

        if (!$league) {
            $this->app->flash('error', 'Cannot find this league');
            $this->app->redirect('/admin/league/'.$league->getId().'/list');
        }

        $name = trim(filter_var($this->app->request()->post('name'), FILTER_SANITIZE_STRING));
        $roundDatesPost = $this->app->request()->post('roundDate');

        $msg = [];
        if (!$name) {
            $msg[] = 'Invalid tournament name';
        }
        $roundDates = [];
        foreach ($roundDatesPost as $k => $date) {
            $d = $this->validDate($date);
            if (!$d) {
                $msg[] = 'Invalid date: '.$date;
            } else {
                $roundDates[] = $d;
            }
        }

        $homeRounds = $this->app->request()->post('home');
        $homeRoundsCount = count($homeRounds);

        $visitorRounds = $this->app->request()->post('visitor');
        $visitorRoundsCount = count($homeRounds);
        if ($homeRoundsCount != $visitorRoundsCount) {
            $msg[] = 'Visitor and Home rounds do not match';
        }
        if (count($msg) > 0) {
            $this->app->flash('error', implode('<br />', $msg));
            $this->app->redirect('/admin/league/'.$league->getId().'/tournament/add');
        }

        $em = $this->app->container->get('doctrine.entitymanager');
        $teamRepository = $em->getRepository('Pool\Entity\Team');
        $tournament = new Tournament();
        $tournament->setName($name);

        $gamesCount = count($homeRounds[0]);

        for ($i = 0; $i < $homeRoundsCount; $i++) {
            $round = new Round();
            $round->setDate($roundDates[$i]);
            for ($j = 0; $j < $gamesCount; $j++) {
                $game = new Game();
                $game->setRound($round);
                $homeId = $homeRounds[$i][$j];
                $visitorId = $visitorRounds[$i][$j];
                if ($homeId < 1) {
                    $team = null;
                } else {
                    $team = $teamRepository->find($homeId);
                }
                $game->setHome($team);
                if ($visitorId < 1) {
                    $team = null;
                } else {
                    $team = $teamRepository->find($visitorId);
                }
                $game->setVisitor($team);
                $round->addGame($game);
            }
            $tournament->addRound($round);
        }
        $league->addTournament($tournament);

        $em->persist($tournament);
        $em->persist($league);
        $em->flush();

        $this->app->redirect('/admin/league/'.$league->getId().'/show');
    }

    /**
     * GET /admin/tournament/:id/show.
     */
    public function showAction($id)
    {
        if (!$id) {
            $this->app->flash('error', 'Cannot find this tournament');
            //$this->app->redirect('/admin/league/list');
        }
        $em = $this->app->container->get('doctrine.entitymanager');
        $tournamentRepository = $em->getRepository('Pool\Entity\Tournament');

        $tournament = $tournamentRepository->find($id);
        if (!$tournament) {
            $this->app->flash('error', 'Cannot find this tournament');
            $this->app->redirect('/admin/league/list');
        }
        $league = $tournament->getLeague();
        $rounds = $tournament->getRounds();

        $this->app->render('admin/showTournament.html', [
            'league' => $league,
            'tournament' => $tournament,
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
