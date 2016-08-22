<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-19
 * Time: 23:36
 */

namespace Api\Controllers;


use Aigisu\Alert\Alert;
use Aigisu\Controller;
use Models\User;
use Slim\Http\Request;
use Slim\Http\Response;

class UserController extends Controller
{
    public function actionIndex(Request $request, Response $response)
    {
        return $response->withJson(User::all()->toArray(), 200);
    }

    public function actionView()
    {
        return $this->render('user/view');
    }

    public function actionCreate(Request $request, Response $response)
    {
        $user = new User([
            'name' => $request->getParam('name'),
            'email' => $request->getParam('email'),
            'password_hash' => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
        ]);

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