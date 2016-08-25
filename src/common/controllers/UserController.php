<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-19
 * Time: 23:36
 */

namespace Aigisu\Common\Controllers;


use Aigisu\Api\Models\User;
use Slim\Http\Request;
use Slim\Http\Response;

class UserController extends Controller
{
    public function actionIndex()
    {
        return $this->render('user/index');
    }

    public function actionView()
    {
        return $this->render('user/view');
    }

    public function actionCreate(Request $request, Response $response)
    {
        $user = new User($request->getParams());

        return $this->render('/auth/register', ['user' => $user]);
    }

    public function actionUpdate()
    {
        return $this->render('/user/edit');
    }

    public function actionDelete()
    {
        return $this->response->withRedirect('/users');
    }
}