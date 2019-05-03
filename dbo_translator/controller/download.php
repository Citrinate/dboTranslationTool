<?php

    /**
     * Class Home
     *
     * Please note:
     * Don't use the same name for class and method, as this might trigger an (unintended) __construct of the class.
     * This is really weird behaviour, but documented here: http://php.net/manual/en/language.oop5.decon.php
     *
     */
class Download extends Controller
{
    // -----------------------------------------------------------------------------------------------------------------

    /**
     *
     */
    public function approved()
    {
        if($this->model->userHasAccess()) {
            $this->model->generateTranslationsFile($this->model->getAllTranslations());
        }
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     *
     */
    public function personal()
    {
        if($this->model->userHasAccess()) {
            $this->model->generateTranslationsFile($this->model->getAllPersonalTranslations());
        }
    }
}