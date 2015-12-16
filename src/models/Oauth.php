<?php

namespace models;

use RedBeanPHP\R;
use RedBeanPHP\OODBBean;
use app\alert\Alert;

class Oauth
{
    const SESSION_NAME = 'oauth';

    public $pin;
    public $errorLog = [];
    private $token;

    public static function tableName()
    {
        return 'oauth';
    }

    public static function isLogged()
    {
        return (isset($_SESSION[self::SESSION_NAME]['run']) && $_SESSION[self::SESSION_NAME]['run']);
    }

    public function run()
    {
        $this->startSession();
    }

    public function getErrorLog()
    {
        return $this->errorLog;
    }

    private function startSession()
    {
        return (session_status() == PHP_SESSION_NONE && !session_id()) ? session_start() : false;
    }

    public function logout()
    {
        $_SESSION[self::SESSION_NAME] = [];
        return session_destroy();
    }

    public function login()
    {
        $_SESSION[self::SESSION_NAME]['run']   = true;
        $_SESSION[self::SESSION_NAME]['token'] = $this->token;
    }

    public function validate()
    {
        if (empty($this->pin)) {
            return false;
        }

        if (strlen($this->pin) != 8) {
            return false;
        }
        return true;
    }

    public function isTimeout($time)
    {
        return ($time - time() < 0);
    }
}