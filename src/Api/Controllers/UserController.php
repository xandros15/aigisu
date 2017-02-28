<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-19
 * Time: 23:36
 */

namespace Aigisu\Api\Controllers;


use Aigisu\Components\Http\UnauthorizedException;
use Aigisu\Models\User;
use Slim\Http\Request;
use Slim\Http\Response;

class UserController extends AbstractController
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionIndex(Request $request, Response $response) : Response
    {
        return $this->read($response, User::all()->toArray());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionView(Request $request, Response $response) : Response
    {
        $user = User::findOrFail($request->getAttribute('id'));

        return $this->read($response, $user->toArray());
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

        return $this->create($response, $this->get('router')->pathFor('api.user.view', ['id' => $user->getKey()]));
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

        return $this->update($response);
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

        return $this->delete($response);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws UnauthorizedException
     */
    public function actionGetCurrent(Request $request, Response $response) : Response
    {
        if ($request->getAttribute('is_guest')) {
            throw new UnauthorizedException($request, $response);
        }

        return $this->read($response, $request->getAttribute('user')->toArray());
    }
}
