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
        return $response->withJson(User::all()->toArray(), self::STATUS_OK);
    }

    public function actionView(Request $request, Response $response)
    {
        /** @var $user User */
        $user = User::findOrFail($request->getAttribute('id'));

        return $response->withJson($user->toArray(), self::STATUS_OK);
    }

    public function actionCreate(Request $request, Response $response)
    {
        $user = new User($request->getParams());
        $user->encryptPassword();
        $user->saveOrFail();

        return $response->withJson($user->toArray(), self::STATUS_CREATED);
    }

    public function actionUpdate(Request $request, Response $response)
    {
        /** @var $user User */
        $user = User::findOrFail($request->getAttribute('id'));

        $user->fill($request->getParams());
        if ($request->getParam('password')) {
            $user->encryptPassword();
        }

        $user->saveOrFail();

        return $response->withJson($user->toArray(), self::STATUS_OK);
    }

    public function actionDelete(Request $request, Response $response)
    {
        $user = User::findOrFail($request->getAttribute('id'));
        $user->delete();

        return $response->withJson($user->toArray(), self::STATUS_OK);
    }
}