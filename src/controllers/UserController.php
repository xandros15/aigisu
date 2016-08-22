<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-19
 * Time: 23:36
 */

namespace Controllers;


use Aigisu\Alert\Alert;
use Aigisu\Controller;
use Models\User;
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
        $user->encryptPassword();
        
        if ($request->isPost() && $user->validate() && $user->save()) {
            Alert::add("Created {$user->name} user");
            return $response->withRedirect('/users');
        }

        return $this->render('/auth/register', ['user' => $user]);
    }

    public function actionUpdate()
    {
        if ($this->request->isPost()) {
            //@todo redirect to updated user
            return $this->response->withRedirect('/users');
        }

        return $this->render('/user/edit');
    }

    public function actionDelete()
    {
        return $this->response->withRedirect('/users');
    }
}