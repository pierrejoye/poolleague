<?php

namespace Pool\Controller;

use Pool\Entity\User;

/**
 * Class AuthController.
 */
class AdminUserController extends ControllerAbstract
{
    protected function validPostData($id)
    {
        $this->checkHash();
        $name = $this->app->request()->post('name');
        $email = $this->app->request()->post('email');
        $password = $this->app->request()->post('password');
        $passwordConfirm = $this->app->request()->post('passwordconfirm');
        $role = $this->app->request()->post('role');
        $idPost = $this->app->request()->post('id');

        $msg = [];

        if ($idPost != $id) {
            $this->app->flash('error', 'Cannot find this user');
            $this->app->redirect('/admin/user/list');

            return;
        }

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
        $data = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role,
            'id' => $id,
        ];

        if (count($msg)) {
            $mode = $id ? 'edit' : 'add';
            $_SESSION['form-data'] = [
                'name' => $name,
                'email' => $email,
                'role' => $role,
                ];
            $this->app->flash('error', implode("\n", $msg));
            $this->app->redirect('/admin/user/'.$mode.'/'.$id.'?valid=1');
        }

        return $data;
    }

    protected function updateOrAddUser($data)
    {
        $em = $this->app->container->get('doctrine.entitymanager');
        if ($data['id']) {
            $userRepository = $em->getRepository('Pool\Entity\User');
            $user = $userRepository->findOneBy(['id' => $data['id']]);
            if (!$user) {
                $this->app->flash('error', 'Cannot find this user');
                $this->app->redirect('/admin/user/list');

                return;
            }
        } else {
            $user = new User();
        }
        $user->setName($data['name']);
        $user->setEmail($data['email']);
        $user->setIsadmin($role == 'admin' ? true : false);
        if (!empty($data['password'])) {
            $user->setPassword($data['password']);
        }
        $em->persist($user);
        $em->flush();
    }

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
        $userRepository = $this->app->container->get('doctrine.entitymanager')->getRepository('Pool\Entity\User');
        $user = $userRepository->find($id);
        if (!$user) {
            $this->flash('error', 'Cannot find this user');
            $this->redirect('/admin/user/list');

            return;
        }

        $data = ['h' => $this->getHash()];
        $data['name'] = $user->getName();
        $data['email'] = $user->getEmail();
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
        $data = $this->validPostData($id);
        $this->updateOrAddUser($data);
        $this->app->redirect('/admin/user/list');
    }

    /**
     * GET /admin/user/add/.
     */
    public function addUserAction()
    {
        $data = $this->validPostData($id);
        $this->updateOrAddUser($data);
        $this->app->redirect('/admin/user/list');
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
        $userRepository = $this->app->container->get('doctrine.entitymanager')->getRepository('Pool\Entity\User');
        $users = $userRepository->findAll();
        $this->app->render('admin/listUser.html', [
            'users' => $users,
        ]);
    }
}

/* vim: set tabstop=4 shiftwidth=4 expandtab: fdm=marker */
