<?php

namespace controller;

use app\core\Controller;
use Slim\Http\Response;
use Slim\Http\Request;
use models\Oauth;
use RedBeanPHP\R;
use RedBeanPHP\OODBBean;
use app\alert\Alert;

class OauthController extends Controller
{

    public function actionIndex(Request $request, Response $response)
    {
        $content = $this->render('oauth/index');
        $response->write($content);
        return $response;
    }

    public function actionLogin(Request $request, Response $response)
    {
        $oauth = new Oauth();
        $oauth->pin = $request->getParam('pin');

        if (!$oauth->validate()) {
            Alert::add('Wrong pin', Alert::ERROR);
        } elseif (($results = R::find(Oauth::tableName(), ' pin = ? ', [$oauth->pin]))) {
            /* @var $result OODBBean */
            $result = reset($results);
            if (!$oauth->isTimeout($result->time)) {
                $response->token = $result->token;
                $oauth->login();
                return $response->withRedirect('/');
            }
            Alert::add('Pin is outdated', Alert::ERROR);
        } else {
            Alert::add('Wrong pin', Alert::ERROR);
        }

        $content = $this->render('oauth/index');
        $response->write($content);
        return $response;
    }

    public function actionLogout(Request $request, Response $response)
    {
        if (Oauth::isLogged()) {
            Oauth::logout();
        }

        return $response->withRedirect('/');
    }
}