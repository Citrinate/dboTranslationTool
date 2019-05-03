<?php

/**
 * Class Home
 *
 * Please note:
 * Don't use the same name for class and method, as this might trigger an (unintended) __construct of the class.
 * This is really weird behaviour, but documented here: http://php.net/manual/en/language.oop5.decon.php
 *
 */
class Register extends Controller
{
    public function index()
    {
        require_once(APP . "classes/Registration.php");
        $registration = new Registration();
        require APP . 'view/login/register.php';
    }
}