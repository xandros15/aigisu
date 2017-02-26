<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-23
 * Time: 02:27
 */

namespace Aigisu\Web\Controllers;


use Aigisu\Components\Auth\SessionAuth;
use Aigisu\Components\Http\BadRequestException;
use Slim\Http\Request;
use Slim\Http\Response;

class AuthController extends AbstractController
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws BadRequestException
     */
    public function actionSignin(Request $request, Response $response) : Response
    {
        $auth = new SessionAuth();
        if (!$auth->isGuest()) {
            throw new BadRequestException($request, $response);
        }
        if (!$auth->signIn($request->getParam('email', ''), $request->getParam('password', ''))) {
            //@todo add alert: you was logged
        }

        return $this->goHome($response);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @throws BadRequestException
     * @return Response
     */
    public function actionSignout(Request $request, Response $response) : Response
    {
        $auth = new SessionAuth();
        if ($auth->isGuest()) {
            throw new BadRequestException($request, $response);
        }
        $auth->singOut();

        return $this->goHome($response);
    }
}
