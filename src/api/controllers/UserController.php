<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-19
 * Time: 23:36
 */

namespace Aigisu\Api\Controllers;


use Aigisu\Api\Models\User;
use Slim\Http\Request;
use Slim\Http\Response;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionIndex(Request $request, Response $response) : Response
    {
        return $response->withJson(User::all()->toArray(), self::STATUS_OK);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionView(Request $request, Response $response) : Response
    {
        $user = User::findOrFail($request->getAttribute('id'));

        return $response->withJson($user->toArray(), self::STATUS_OK);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionCreate(Request $request, Response $response) : Response
    {
        $user = new User($request->getParams());
        $user->saveOrFail();

        return $response->withJson($user->toArray(), self::STATUS_CREATED);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionUpdate(Request $request, Response $response) : Response
    {
        $user = User::findOrFail($request->getAttribute('id'));
        $user->fill($request->getParams());
        $user->saveOrFail();

        return $response->withJson($user->toArray(), self::STATUS_OK);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function actionDelete(Request $request, Response $response) : Response
    {
        $user = User::findOrFail($request->getAttribute('id'));
        $user->delete();

        return $response->withJson($user->toArray(), self::STATUS_OK);
    }
}