<?php

namespace Pool\Controller;

use Pool\Entity\User;

/**
 * Class AuthController.
 */
class AdminUserController extends ControllerAbstract
{
    /**
     * GET /login.
     */
    public function addUserFormAction()
    {
        $data = ['h' => $this->getHash()];
        if ($this->app->request()->get('valid')) {
            $data = array_merge($data, $_SESSION['form-data']);
        }
        $this->app->render('admin/addUser.html', $data);
    }

    /**
     * GET /admin/user/edit/:id.
     */
    public function editFormAction($id)
    {
        $userRepository = $this->app->container->get('user.repository');
        $user = $userRepository->find($id);
        if (!$user) {
            $this->flash('error', 'Cannot find this user');
            $this->redirect('/admin/user/list');

            return;
        }

        $data = ['h' => $this->getHash()];

        $data['name'] = $user->getName();
        $data['email'] = $user->getEmail();
        $data['role'] = $user->getRole();
        $data['id'] = $user->getId();
        $data['mode'] = 'edit';
        $this->app->render('admin/addUser.html', $data);

        return;
    }

    /**
     * POST /admin/user/edit/:id.
     */
    public function updateAction($id)
    {
        $this->checkHash();
        $idPost = $this->app->request()->post('id');
        $userRepository = $this->app->container->get('user.repository');
        if (!$userRepository->find($id) || $id != $idPost) {
            $this->app->flash('error', 'Cannot find this user');
            $this->app->redirect('/admin/user/list');

            return;
        }

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

        if (count($msg)) {
            $_SESSION['form-data'] = [
                'name' => $name,
                'email' => $email,
                ];
            $this->app->flash('error', implode("\n", $msg));
            $this->app->redirect('/admin/user/add?valid=1');
        }

        $user = new User();
        $user->setName($name);
        $user->setEmail($email);
        $user->setRole($role);
        $user->setId($id);
        if (!empty($password)) {
            $user->setPassword($password);
        }

        $userRepository = $this->app->container->get('user.repository');
        $user = $userRepository->persist($user);
        $this->app->redirect('/admin/user/list');
    }

    /**
     * GET /admin/user/add/.
     */
    public function addUserAction()
    {
        $this->checkHash();

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

        if (empty($password) || empty($passwordConfirm)
                || ($password !== $passwordConfirm)
                || (strlen($password) < 5)
            ) {
            $msg[] = 'Passwords do not match or are too short, five characters minimum';
        }

        $userRepository = $this->app->container->get('user.repository');
        if ($userRepository->findByEmail($email)) {
            $msg[] = 'This emal is already used, email can only be used for one user';
        }

        if (count($msg)) {
            $_SESSION['form-data'] = [
                'name' => $name,
                'email' => $email,
                ];
            $this->app->flash('error', implode("\n", $msg));
            $this->app->redirect('/admin/user/add?valid=1');

            return;
        }

        $user = new User();
        $user->setName($name);
        $user->setEmail($email);
        $user->setRole($role);
        $user->setPassword($password);

        $userRepository = $this->app->container->get('user.repository');
        $user = $userRepository->persist($user);
        $this->app->redirect('/admin/user/add');
    }

    /**
     * GET /admin/user/list/.
     */
    public function removeUserAction($id)
    {
        $userRepository = $this->app->container->get('user.repository');
        $user = $userRepository->find($id);
        if (!$user) {
            $this->app->flash('error', 'cannot find this user');
            $this->app->redirect('/admin/user/list');

            return;
        }

        $teamRepository = $this->app->container->get('team.repository');
        $teams = $teamRepository->getAll();
        foreach ($teams as $team) {
            $players = $team->getPlayers();
            if (in_array($id, $players)) {
                $k = array_search($id, $players);
                unset($players[$k]);
                $team->setPlayersFromId($players);
                $teamRepository->persist($team);
            }
        }

        $userRepository->remove($user);
        $this->app->redirect('/admin/user/list');
    }

    /**
     * GET /admin/user/list/.
     */
    public function listAction()
    {
        $userRepository = $this->app->container->get('user.repository');
        $users = $userRepository->getAll();
        $this->app->render('admin/listUser.html', [
            'users' => $users,
        ]);
    }
}

/* vim: set tabstop=4 shiftwidth=4 expandtab: fdm=marker */
