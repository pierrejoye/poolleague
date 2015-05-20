<?php

namespace Pool\Controller;

use Pool\Entity\User;
use Pool\Entity\Team;

/**
 * Class AuthController.
 */
class AdminTeamController extends ControllerAbstract
{
    protected function getCaptainList()
    {
        $userRepository = $this->app->container->get('user.repository');
        $users = $userRepository->getAll();
        $selectCaptain = [];
        foreach ($users as $user) {
            $selectCaptain[$user->getId()] = $user->getName();
        }

        return $selectCaptain;
    }

    /**
     * GET /login.
     */
    public function editFormAction($id)
    {
        $teamRepository = $this->app->container->get('team.repository');
        $team = $teamRepository->find($id);
        if (!$team) {
            $this->app->flash('error', 'Cannot find this team');
            $this->app->redirect('/admin/team/list');

            return;
        }

        $selectCaptain = $this->getCaptainList();
        $data = ['h' => $this->getHash()];
        $data['selectCaptain'] = $selectCaptain;
        $data['id'] = $team->getId();
        $data['name'] = $team->getName();
        $data['captain'] = $team->getCaptainId();
        $data['mode'] = 'edit';

        $this->app->render('admin/addTeam.html', $data);

        return;
    }

    /**
     * POST /login.
     */
    public function updateAction($id)
    {
        $this->checkHash();
        $edit = $this->app->request()->post('edit') == 1 ? true : false;
        $name = $this->app->request()->post('name');
        $captain = $this->app->request()->post('captain');
        $postId = $this->app->request()->post('id');

        if ($id != $postId) {
            $this->app->flash('error', 'Invalid form, IDs do not match');
            $this->app->redirect('/admin/team/list');

            return;
        }

        $teamRepository = $this->app->container->get('team.repository');
        if (!$teamRepository->find($id)) {
            $this->app->flash('error', 'Cannot find this team');
            $this->app->redirect('/admin/team/list');
        }

        $msg = [];

        $name = filter_var($name, FILTER_SANITIZE_STRING);
        if (strlen($name) < 2) {
            $msg[] = 'Invalid name';
        }
        $userRepository = $this->app->container->get('user.repository');
        $captain = $userRepository->find($captain);
        if (!$captain) {
            $msg[] = 'Cannot find this user to use as captain';
        }

        if (count($msg)) {
            $_SESSION['form-data'] = [
                'name' => $name,
                'captain' => $captain,
                'id' => $id,
                ];
            $this->app->flash('error', implode("\n", $msg));
            $this->app->redirect('/admin/team/add?valid=1');

            return;
        }

        $team = new Team();
        $team->setName($name);
        $team->setCaptainId($captain->getId());
        $team->setId($id);

        $team = $teamRepository->persist($team);
        $this->app->redirect('/admin/team/list');
    }

    /**
     * GET /admin/team/add.
     */
    public function addFormAction()
    {
        $data = ['h' => $this->getHash()];
        if ($this->app->request()->get('valid')) {
            $data = array_merge($data, $_SESSION['form-data']);
        }
        $data['mode'] = 'add';
        $data['selectCaptain'] = $this->getCaptainList();
        $this->app->render('admin/addTeam.html', $data);
    }

    /**
     * POST /admin/team/add.
     */
    public function addAction()
    {
        $this->checkHash();
        $name = $this->app->request()->post('name');
        $captain = $this->app->request()->post('captain');

        $msg = [];

        $name = filter_var($name, FILTER_SANITIZE_STRING);
        if (strlen($name) < 2) {
            $msg[] = 'Invalid name';
        }

        $userRepository = $this->app->container->get('user.repository');
        $captain = $userRepository->find($captain);
        if (!$captain) {
            $msg[] = 'cannot find this captain';
        }

        if (count($msg)) {
            $_SESSION['form-data'] = [
                'name' => $name,
                'captain' => $captain,
                ];
            $this->app->flash('error', implode("\n", $msg));
            $this->app->redirect('/admin/team/add?valid=1');

            return;
        }
        $team = new Team();
        $team->setName($name);
        $team->setCaptainId($captain->getId());
        $teamRepository = $this->app->container->get('team.repository');
        $teamRepository->persist($team);

        $this->app->redirect('/admin/team/add');
    }

    public function listAction()
    {
        $teamRepository = $this->app->container->get('team.repository');
        $userRepository = $this->app->container->get('user.repository');
        $teams = $teamRepository->getAll();
        if (!$teams) {
            $teams = [];
        } else {
            $captains = [];
            foreach ($teams as $team) {
                $captain = $userRepository->find($team->getCaptainId());
                if ($captain) {
                    $captains[$team->getId()] = $captain->getName();
                } else {
                    $captains[$team->getId()] = '';
                }
            }
        }
        $this->app->render('admin/listTeam.html', [
            'teams' => $teams,
            'captains' => $captains,
        ]);
    }
}

/* vim: set tabstop=4 shiftwidth=4 expandtab: fdm=marker */
