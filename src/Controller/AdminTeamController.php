<?php

namespace Pool\Controller;

use Pool\Entity\User;
use Pool\Entity\Team;

/**
 * Class AdminTeamController.
 */
class AdminTeamController extends ControllerAbstract
{
    protected function isAlreadyCaptain($id)
    {
        $em = $this->app->container->get('doctrine.entitymanager');
        $teams = $em->getRepository('Pool\Entity\Team')->find($id);
        if (!$teams) {
            return false;
        }
        foreach ($teams as $team) {
            if ($team->getCaptainId() == $id) {
                return true;
            }
        }

        return false;
    }

    protected function getCaptainList($id = false)
    {
        $em = $this->app->container->get('doctrine.entitymanager');
        $users = $em->getRepository('Pool\Entity\User')->findAll();
        $selectCaptain = [];
        foreach ($users as $user) {
            $selectCaptain[$user->getId()] = $user->getName();
        }

        return  $selectCaptain;
    }

    /**
     * GET /login.
     */
    public function editFormAction($id)
    {
        $em = $this->app->container->get('doctrine.entitymanager');
        $team = $em->getRepository('Pool\Entity\Team')->find($id);

        if (!$team) {
            $this->app->flash('error', 'Cannot find this team');
            $this->app->redirect('/admin/team/list');

            return;
        }

        $selectCaptain = $this->getCaptainList($id);
        if (!$selectCaptain) {
            $this->app->flash('error', 'No captain available');
            $this->redirect('/admin/team/list');

            return;
        }
        $data = ['h' => $this->getHash()];
        $data['selectCaptain'] = $selectCaptain;
        $data['id'] = $team->getId();
        $data['name'] = $team->getName();
        $data['captain'] = $team->getCaptain()->getId();
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
        $em = $this->app->container->get('doctrine.entitymanager');
        $team = $em->getRepository('Pool\Entity\Team')->find($id);

        if (!$team) {
            $this->app->flash('error', 'Cannot find this team');
            $this->app->redirect('/admin/team/list');
        }

        $msg = [];

        $name = filter_var($name, FILTER_SANITIZE_STRING);
        if (strlen($name) < 2) {
            $msg[] = 'Invalid name';
        }
        $userRepository = $em->getRepository('Pool\Entity\user');
        $captain = $userRepository->find($captain);
        if (!$captain) {
            $msg[] = 'Cannot find this user (as captain)';
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

        $team->setName($name);
        $team->setCaptain($captain);
        $em->persist($team);
        $em->flush();
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

        $selectCaptain = $this->getCaptainList($id);
        $data['mode'] = 'add';
        $data['selectCaptain'] = $selectCaptain;
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
        $name = filter_var($name, FILTER_SANITIZE_STRING);
        $captain = filter_var($captain, FILTER_VALIDATE_INT);

        $msg = [];
        if (strlen($name) < 2) {
            $msg[] = 'Invalid name';
        }

        $em = $this->app->container->get('doctrine.entitymanager');
        $captain = $em->getRepository('Pool\Entity\User')->find($captain);
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
        $team->setCaptain($captain);
        $em->persist($team);
        $em->flush();
        $this->app->redirect('/admin/team/add');
    }

    public function listAction()
    {
        $em = $this->app->container->get('doctrine.entitymanager');
        $teams = $em->getRepository('Pool\Entity\Team')->findAll();
        if (!$teams) {
            $teams = [];
        } else {
            $captains = [];
            foreach ($teams as $team) {
                $captain = $team->getCaptain();
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
