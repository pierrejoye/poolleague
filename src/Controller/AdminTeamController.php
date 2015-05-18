<?php

namespace Pool\Controller;

use Pool\Entity\User;
use Pool\Entity\Team;

/**
 * Class AuthController.
 */
class AdminTeamController extends ControllerAbstract
{
    /**
     * GET /admin/team/add.
     */
    public function addFormAction()
    {
        $data = ['h' => $this->getHash()];
        if ($this->app->request()->get('valid')) {
            $data = array_merge($data, $_SESSION['form-data']);
        }
        $this->app->render('admin/addTeam.html', $data);
    }

    /**
     * GET /login.
     */
    public function editFormAction()
    {
        $id = $this->app->request->get('id');
        if (!$id) {
            $this->flash('error', 'Cannot find this user');
            $this->redirect('/admin/team/list');

            return;
        }

        $data = ['h' => $this->getHash()];
        $userRepository = $this->app->container->get('user.repository');
        $user = $userRepository->find($id);
        if (!$user) {
            $this->flash('error', 'Cannot find this user');
            $this->redirect('/admin/team/list');

            return;
        }
        $data['name'] = $user->getName();
        $data['email'] = $user->getEmail();
        $data['role'] = $user->getRole();
        $data['mode'] = 'edit';
        $this->app->render('admin/addTeam.html', $data);

        return;
    }

    /**
     * POST /login.
     */
    public function updateAction()
    {
        $this->checkHash();
        $edit = $this->app->request()->post('edit') == 1 ? true : false;

        $name = $this->app->request()->post('name');
        $email = $this->app->request()->post('email');
        $password = $this->app->request()->post('password');
        $passwordConfirm = $this->app->request()->post('passwordconfirm');
        $role = $this->app->request()->post('role');

        $msg = [];

        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $msg[] = 'Invalid email';
        }

        $name = filter_var($name, FILTER_SANITIZE_STRING);
        if (strlen($name) < 2) {
            $msg[] = 'Invalid name';
        }

        if (!in_array($role, ['captain', 'player', 'admin'])) {
            $msg[] = 'Invalid role';
        }

        if (!empty($password)) {
            if ($password !== $passwordConfirm) {
                $msg[] = 'Passwords do not match or are too short, five characters minimum';
            }
        }

        $userRepository = $this->app->container->get('user.repository');
        if (!$TeamRepository->find($team)) {
            $msg[] = 'Cannot find this user';
            $this->app->redirect('/admin/user/list');
        }

        if (count($msg)) {
            $_SESSION['form-data'] = [
                'name' => $name,
                'email' => $email,
                ];
            $this->app->flash('error', implode("\n", $msg));
            $this->app->redirect('/admin/user/add?valid=1');
        }

        $user = new Team();
        $user->setName($name);
        $user->setCaptain($email);

        $userRepository = $this->app->container->get('team.repository');
        $user = $userRepository->persist($user);
        $this->app->redirect('/admin/team/list');
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
        if (!$userRepository->find($captain)) {
            $msg[] = 'cannot find this captain';
        }

        if (count($msg)) {
            $_SESSION['form-data'] = [
                'name' => $name,
                'captain' => $captain,
                ];
            $this->app->flash('error', implode("\n", $msg));
            //$this->app->redirect('/admin/team/add?valid=1');
            return;
        }

        $team = new Team();
        $team->setName($name);
        $team->setCaptain($captain);
        $id = $team->getId();
        $teamRepository = $this->app->container->get('team.repository');
        $teamRepository->persist($team);
        $this->app->redirect('/admin/team/add');
    }

    public function listAction()
    {
        $teamRepository = $this->app->container->get('team.repository');
        $teams = $teamRepository->getAll();
        $this->app->render('admin/listTeam.html', [
            'teams' => $teams,
        ]);
    }
}

/* vim: set tabstop=4 shiftwidth=4 expandtab: fdm=marker */
