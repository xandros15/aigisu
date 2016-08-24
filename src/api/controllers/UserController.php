<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-19
 * Time: 23:36
 */

namespace Api\Controllers;


use Api\ApiController;
use Models\User;
use Slim\Http\Request;
use Slim\Http\Response;

class UserController extends ApiController
{
    public function actionIndex(Request $request, Response $response)
    {
        $users = User::all();
        if ($users->isEmpty()) {
            return $response->withJson([self::MESSAGE => 'Users not found'], self::STATUS_NOT_FOUND);
        }
        return $response->withJson($users->toArray(), self::STATUS_OK);
    }

    public function actionView(Request $request, Response $response)
    {
        $user = User::find($request->getAttribute('id'));
        if (!$user) {
            return $response->withJson([self::MESSAGE => 'User not found'], self::STATUS_NOT_FOUND);
        }
        return $response->withJson($user->toArray(), self::STATUS_OK);
    }

    public function actionCreate(Request $request, Response $response)
    {
        $user = new User($request->getParams());
        $user->encryptPassword();

        if (!$user->save()) {
            return $response->withJson([self::MESSAGE => 'Can\'t save a User'], self::STATUS_NOT_FOUND);
        }
        return $response->withJson($user->toArray(), self::STATUS_CREATED);
    }

    public function actionUpdate(Request $request, Response $response)
    {
        $user = User::find($request->getAttribute('id'));
        if (!$user) {
            return $response->withJson([self::MESSAGE => 'User not found'], self::STATUS_NOT_FOUND);
        }

        $user->fill($request->getParams());
        if ($request->getParam('password')) {
            $user->encryptPassword();
        }

        if (!$user->save()) {
            return $response->withJson([self::MESSAGE => 'Can\'t save a User'], self::STATUS_SERVER_ERROR);
        }

        return $response->withJson($user->toArray(), self::STATUS_OK);
    }

    public function actionDelete()
    {
        return $this->response->withRedirect('/users');
    }
}