<?php

    /**
     * Class Home
     *
     * Please note:
     * Don't use the same name for class and method, as this might trigger an (unintended) __construct of the class.
     * This is really weird behaviour, but documented here: http://php.net/manual/en/language.oop5.decon.php
     *
     */
class Admin extends Controller
{

    // -----------------------------------------------------------------------------------------------------------------

    /**
     */
    public function index()
    {
        if($this->model->userIsAdmin()) {
            if(sizeof($_POST) != 0) {
                $this->model->handleTranslation();
            }

            else {
                $pending = $this->model->getPendingLanguageStrings();
                $pending_by_user = $this->model->getPendingUsers();
                $group_names = $this->model->getGroupNames();

                require APP . 'view/_templates/header.php';
                require APP . 'view/admin/index.php';
                require APP . 'view/_templates/footer.php';
            }
        }

        else {
            $error = "Access denied";
            require APP . 'view/error/index.php';
        }
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     */
    public function users()
    {
        if($this->model->userIsAdmin()) {
            $users = $this->model->getUsers();
            require APP . 'view/_templates/header.php';
            require APP . 'view/admin/users.php';
            require APP . 'view/_templates/footer.php';
        }

        else {
            $error = "Access denied";
            require APP . 'view/error/index.php';
        }
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param $userid
     */
    public function grantaccess($userid)
    {
        if($this->model->userIsAdmin()) {
            $this->model->setAccessStatus($userid, 1);
        }

        header('location: ' . URL . '/admin/users');
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param $userid
     */
    public function revokeaccess($userid)
    {
        if($this->model->userIsAdmin()) {
            $this->model->setAccessStatus($userid, 0);
        }

        header('location: ' . URL . '/admin/users');
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param $userid
     */
    public function setadmin($userid)
    {
        if($this->model->userIsHeadAdmin()) {
            $this->model->setAdminStatus($userid, 1);
        }

        header('location: ' . URL . '/admin/users');
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param $userid
     */
    public function revokeadmin($userid)
    {
        if($this->model->userIsHeadAdmin()) {
            $this->model->setAdminStatus($userid, 0);
            $this->model->setAccessStatus($userid, 1);
        }

        header('location: ' . URL . '/admin/users');
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param $userid
     */
    public function acceptall($userid)
    {
        if($this->model->userIsAdmin()) {
            $this->model->acceptTranslationsForUser($userid);
        }

        header('location: ' . URL . '/admin');
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param $userid
     */
    public function denyall($userid)
    {
        if($this->model->userIsAdmin()) {
            $this->model->denyTranslationsForUser($userid);
        }

        header('location: ' . URL . '/admin');
    }
}