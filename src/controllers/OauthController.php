<?php

namespace Controllers;

use Aigisu\Alert\Alert;
use Aigisu\Controller;
use Models\Oauth;
use Slim\Http\Request;

class OauthController extends Controller
{
    const SESSION_NAME = 'oauth';

    public function actionIndex()
    {
        $this->view->title = 'Oauth';
        $this->view->containerClass = 'container';
        return $this->render('oauth/index');
    }

    public function actionLogin(Request $request)
    {
        $this->view->title = 'Oauth';
        $this->view->containerClass = 'container';
        /** @var $model Oauth */
        $model = Oauth::firstOrNew($request->getParams());
        if (!$model->id) {
            Alert::add('Wrong pin', Alert::ERROR);

            return $this->render('oauth/index');
        }
        if (!$model->validateTime()) {
            Alert::add('Pin is outdated', Alert::ERROR);

            return $this->render('oauth/index');
        }
        $this->login($model);
        return $this->goHome();
    }

    public function login(Oauth $model)
    {
        $_SESSION[self::SESSION_NAME]['run'] = true;
        $_SESSION[self::SESSION_NAME]['token'] = $model->token;
    }

    public function actionLogout()
    {
        if (self::isLogged()) {
            $this->logout();
        }

        return $this->goBack();
    }

    public static function isLogged()
    {
        return (isset($_SESSION[self::SESSION_NAME]['run']) && $_SESSION[self::SESSION_NAME]['run']);
    }

    public static function logout()
    {
        $_SESSION[self::SESSION_NAME] = [];
        return session_destroy();
    }
}