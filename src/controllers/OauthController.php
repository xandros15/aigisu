<?php

namespace controller;

use app\core\Controller;
use Slim\Http\Request;
use models\Oauth;
use app\alert\Alert;

class OauthController extends Controller
{
    const SESSION_NAME = 'oauth';

    public static function isLogged()
    {
        return (isset($_SESSION[self::SESSION_NAME]['run']) && $_SESSION[self::SESSION_NAME]['run']);
    }

    public function actionIndex()
    {
        return $this->render('oauth/index');
    }

    public function actionLogin(Request $request)
    {
        $model = Oauth::firstOrNew($request->getParams());
        if (!$model->id) {
            Alert::add('Wrong pin', Alert::ERROR);

            return $this->render('oauth/index');
        }
        if (!$model->validateTime()) {
            Alert::add('Pin is outdated', Alert::ERROR);

            return $this->render('oauth/index');
        }
        $this->login();
        return $this->goHome();
    }

    public function actionLogout()
    {
        if (self::isLogged()) {
            $this->logout();
        }

        return $this->goBack();
    }

    public static function logout()
    {
        $_SESSION[self::SESSION_NAME] = [];
        return session_destroy();
    }

    public function login()
    {
        $_SESSION[self::SESSION_NAME]['run']   = true;
        $_SESSION[self::SESSION_NAME]['token'] = $this->token;
    }
}