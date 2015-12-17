<?php

namespace controller;

use app\core\Controller;
use Slim\Http\Request;
use models\Oauth;
use RedBeanPHP\R;
use app\alert\Alert;

class OauthController extends Controller
{

    public function actionIndex()
    {
        return $this->render('oauth/index');
    }

    public function actionLogin(Request $request)
    {
        $oauth      = new Oauth();
        $oauth->pin = $request->getParam('pin');

        $results = ($oauth->validate()) ? R::findOne(Oauth::tableName(), ' pin = ? ', [$oauth->pin]) : false;
        if (!$results) {
            Alert::add('Wrong pin', Alert::ERROR);

            return $this->render('oauth/index');
        }
        if ($oauth->isTimeout($results->time)) {
            Alert::add('Pin is outdated', Alert::ERROR);

            return $this->render('oauth/index');
        }

        $oauth->login();
        Alert::add('You have been logged in');

        return $this->goHome();
    }

    public function actionLogout()
    {
        if (Oauth::isLogged()) {
            Oauth::logout();
        }

        return $this->goBack();
    }
}