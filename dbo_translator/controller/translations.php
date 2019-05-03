<?php

    /**
     * Class Home
     *
     * Please note:
     * Don't use the same name for class and method, as this might trigger an (unintended) __construct of the class.
     * This is really weird behaviour, but documented here: http://php.net/manual/en/language.oop5.decon.php
     *
     */
class Translations extends Controller
{
    public function view($version = null, $file = null, $type = null, $page = 1, $sub_page = 1, $mode = 0, $search_mode = 0, $search_query = null)
    {
        if(!$this->model->userHasAccess()) {
            header('location: ' . URL . '/');
        }

        /* sanitize inputs */
        else if(!is_numeric($version) || !is_numeric($file) || !is_numeric($type)
            || !is_numeric($page) || !is_numeric($sub_page) || !is_numeric($mode) || !is_numeric($search_mode)
        ) {
            $error = "Invalid translations page";
            require APP . 'view/error/index.php';
        }

        else {
            if(count($_POST) > 0) {
                if(isset($_POST["pagenum"]) && is_numeric($_POST["pagenum"])) {
                    /* jump to page */
                    header("location: " . URL . "/translations/view/{$version}/{$file}/{$type}/{$page}/{$_POST["pagenum"]}/{$mode}/{$_POST["options"]}/{$_POST["query"]}");
                } else if(isset($_POST["query"]) && isset($_POST["options"])) {
                    /* move the search parameters from POST to the url */
                    header("location: " . URL . "/translations/view/{$version}/{$file}/{$type}/{$page}/1/{$mode}/{$_POST["options"]}/{$_POST["query"]}");
                } else {
                    /* submit data */
                    $this->model->submitStrings($version, $file, $type);
                }
            }

            $strings = $this->model->getLanguageStrings($version, $file, $type, $page, $sub_page, $mode, $search_mode, $search_query);
            $num_pages = $strings === false ? 0 : $this->model->getNumPages();
            $group_names = $this->model->getGroupNames();
            $title = $group_names[$file][$type];

            require APP . 'view/_templates/header.php';
            require APP . 'view/translations/index.php';
            require APP . 'view/_templates/footer.php';
        }
    }
}