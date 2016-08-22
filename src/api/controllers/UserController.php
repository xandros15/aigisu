<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-19
 * Time: 23:36
 */

namespace Api\Controllers;


use Aigisu\Controller;
use Models\User;
use Slim\Http\Request;
use Slim\Http\Response;

class UserController extends Controller
{
    public function actionIndex(Request $request, Response $response)
    {
        $users = User::all();
        if ($users->isEmpty()) {
            return $response->withJson(['error' => 'Users not found'], 404);
        }
        return $response->withJson($users->toArray(), 200);
    }

    public function actionView(Request $request, Response $response)
    {
        $user = User::find($request->getAttribute('id'));
        if (!$user) {
            return $response->withJson(['error' => 'User not found'], 404);
        }
        return $response->withJson($user->toArray(), 200);
    }

    public function actionCreate(Request $request, Response $response)
    {
        $user = new User($request->getParams());
        $user->encryptPassword();

        if (!($user->validate() && $user->save())) {
            return $response->withJson(['error' => $user->getErrors()], 404); //@todo correct message and code
        }
        return $response->withJson($user->toArray(), 201);
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