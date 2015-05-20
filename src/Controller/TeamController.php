<?php

namespace Pool\Controller;

use Pool\Entity\User;

/**
 * Class TeamController.
 */
class TeamController extends ControllerAbstract
{
    protected function getTeam($id)
    {
        $teamRepository = $this->app->container->get('team.repository');
        $team = $teamRepository->find($id);
        if (!$team) {
            $this->app->flash('error', 'cannot find this team');
            $this->app->redirect('/team/list');

            return;
        }

        return $team;
    }

    /**
     * GET /team/:id/player/add.
     */
    public function PlayerAddForm($teamId)
    {
        $team = $this->getTeam($teamId);
        $players = $team->getPlayers();
        if (count($players) < 1) {
            $players = array_fill(0, 20, '');
        }
        $players = array_merge($players, array_fill(0, 20 - count($players), ''));
        $this->app->render('team/playerEdit.html', [
            'team' => $team,
            'players' => $players,
            'h' => $this->getHash(),
        ]
        );
    }

    /**
     * POST /team/:id/player/add.
     */
    public function PlayerAdd($teamId)
    {
        $this->getHash();
        $team = $this->getTeam($teamId);
        if ($this->app->request()->post('id') != $teamId) {
            $this->flash('error', 'invalid team Id');
            $this->redirect('/admin/team/list');

            return;
        }

        $namesPost = $this->app->request()->post('names');
        $emailsPost = $this->app->request()->post('emails');

        $msg = [];
        foreach ($namesPost as $k => $name) {
            if (empty($name) && empty($emailsPost[$k])) {
                continue;
            }
            if (!empty($name)) {
                if (empty($emailsPost[$k])) {
                    $msg[] = 'Please enter '.$name.' email';
                } else {
                    $players[] = [
                        'name' => $name,
                        'email' => $emailsPost[$k],
                    ];
                }
            }
        }

        if (count($msg) > 0) {
            $this->app->flash('error', implode($msg, '<br/>'));
            die(implode($msg, '<br/>'));
            $this->app->redirect('/team/'.$team->getId().'/list');
        }

        $userRepository = $this->app->container->get('user.repository');
        $users = [];
        foreach ($players as $player) {
            $user = new User();
            $user->setEmail($player['email']);
            $user->setRole('player');
            $userRepository->persist($user);
            $users[] = $user;
        }

        $team->setPlayers($users);
        $teamRepository = $this->app->container->get('team.repository');
        $teamRepository->persist($team);
        $this->app->redirect('/team/'.$team->getId().'/player/list');
    }

    /**
     * GET /team/:id/player/list.
     */
    public function playerList($teamId)
    {
        $team = $this->getTeam($teamId);
        $this->app->render('team/playerList.html', [
        'team' => $team,
        'user' => $this->app->user(),
        ]);
    }
}

/* vim: set tabstop=4 shiftwidth=4 expandtab: fdm=marker */
