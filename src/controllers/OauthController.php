<?php

namespace controller;

use app\core\Controller;
use Slim\Http\Response;
use Slim\Http\Request;

class OauthController extends Controller
{

    public function actionIndex(Request $request, Response $response)
    {
        $content = $this->render('oauth/index');
        $response->write($content);
        return $response;
    }
}