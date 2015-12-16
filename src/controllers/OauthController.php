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
        /* @var $oauth Oauth */
        global $oauth;

        $oauth->pin = $request->getParam('pin');

        if (!$oauth->validate()) {
            Alert::add('Wrong pin', Alert::ERROR);
        } elseif (($results = R::find(Oauth::tableName(), ' pin = ? ', [$oauth->pin]))) {
            /* @var $result OODBBean */
            $result = reset($results);
            if ($oauth->isTimeout($result->time)) {
                Alert::add('Pin is outdated', Alert::ERROR);
                return $response;
            }
            $response->token = $result->token;
            $oauth->login();
            return $response->withRedirect('/');
        } else {
            Alert::add('Wrong pin', Alert::ERROR);
        }

        $content = $this->render('oauth/index');
        $response->write($content);
        return $response;
    }

    public function actionLogout(Request $request, Response $response)
    {
        /* @var $oauth Oauth */
        global $oauth;

        if (Oauth::isLogged()) {
            $oauth->logout();
        }

        return $response->withRedirect('/');
    }
}