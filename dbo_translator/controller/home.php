<?php

/**
 * Class Home
 *
 * Please note:
 * Don't use the same name for class and method, as this might trigger an (unintended) __construct of the class.
 * This is really weird behaviour, but documented here: http://php.net/manual/en/language.oop5.decon.php
 *
 */
class Home extends Controller
{
    public function index()
    {
        if(isset($_GET["logout"])) {
            header('location: ' . URL . '/');
        }

        else if(!$this->model->isLoggedIn()) {
            require APP . 'view/login/not_logged_in.php';
        }

        else if($this->model->userHasAccess()) {
            $title = "DBO Translation Tool";
            $complete = $this->model->getGroupCompletion();
            $group_names = $this->model->getGroupNames();
            $groups = array(
                array(
                    "title"   => "Items & Skills",
                    "color"   => "red",
                    "members" => array(
                        array(2,8), array(2,3)
                    )
                ),
                array(
                    "title"   => "User Interface",
                    "color"   => "orange",
                    "members" => array(array(0,0), array(1,0), array(4,0))
                ),
                array(
                    "title"   => "Names",
                    "color"   => "",
                    "members" => array(array(2,26), array(2,5), array(2,6),
                                       array(2,10), array(2,20), array(2,27),
                                       array(2,11), array(2,12), array(2,7),
                                       array(2,0), array(2,4))
                ),
                array(
                    "title"   => "Quests",
                    "color"   => "green",
                    "members" => array(array(3,0), array(2,23), array(2,19))
                ),
                array(
                    "title"   => "Dialog",
                    "color"   => "purple",
                    "members" => array(array(2,22), array(2,16), array(2,17))
                ),
                array(
                    "title"   => "Other",
                    "color"   => "light_blue",
                    "members" => array(array(2,1), array(2,14), array(2,24),
                                       array(2,25), array(2,18), array(2,2))
                ),
                array(
                    "title"   => "Unknown",
                    "color"   => "brown",
                    "members" => array(array(2,9), array(2,15), array(2,13))
                )
            );

            require APP . 'view/_templates/header.php';
            require APP . 'view/home/index.php';
            require APP . 'view/_templates/footer.php';
        }

        else {
            $error = "Access denied, please wait until an admin grants you access, then log in again. " .
                '<a href="' . URL . '/?logout">Log out</a>';
            require APP . 'view/error/index.php';
        }
    }
}
