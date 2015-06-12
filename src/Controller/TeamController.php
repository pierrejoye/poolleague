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
        $em = $this->app->container->get('doctrine.entitymanager');
        $team = $em->getRepository('Pool\Entity\Team')->find($id);
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
        if (!$players) {
            $players = [];
        }
        $em = $this->app->container->get('doctrine.entitymanager');

        $toAdd = 20 - count($players) - 1;

        $players[0] = $team->getCaptain();
        for ($i = 0; $i < $toAdd; $i++) {
            $players[] = '';
        }

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
        $idPost = $this->app->request()->post('id');

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
                        'id' => $idPost[$K],
                    ];
                }
            }
        }

        if (count($players) < 1) {
            $msg[] = 'No user to add';
        }

        if (count($msg) > 0) {
            $this->app->flash('error', implode($msg, '<br/>'));
            $this->app->redirect('/team/'.$team->getId().'/list');
        }
        $currentPlayers = $team->getPlayers();
        $em = $this->app->container->get('doctrine.entitymanager');
        $userRepository = $em->getRepository('Pool\Entity\User');
        /*
         * Check if user already registered
         * Add non existing users
         * Just assign existing users to the team if he exists
         */

        foreach ($players as $player) {
            $existingUser = $userRepository->findOneBy(['email' => $player['email']]);
            if ($existingUser) {
                $user = $existingUser;
                foreach ($currentPlayers as $p) {
                    if ($p->getEmail() == $user->getEmail()) {
                        continue 2;
                    }
                }
            } else {
                echo $player['email']."not found\n";
                $user = new User();
                $user->setName($player['name']);
                $user->setEmail($player['email']);
                $em->persist($user);
            }
            $team->addPlayer($user);
        }

        $em->persist($team);
        $em->flush();
        $this->app->redirect('/team/'.$team->getId().'/player/list');
    }

    /**
     * GET /team/:id/player/list.
     */
    public function playerList($teamId)
    {
        $team = $this->getTeam($teamId);

        $em = $this->app->container->get('doctrine.entitymanager');
        $team = $em->getRepository('Pool\Entity\Team')->find($teamId);
        $players = $team->getPlayers();

        $this->app->render('team/playerList.html', [
        'team' => $team,
        'players' => $players,
        'user' => $this->app->user(),
        ]);
    }
}

/* vim: set tabstop=4 shiftwidth=4 expandtab: fdm=marker */
