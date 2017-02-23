<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-23
 * Time: 02:27
 */

namespace Aigisu\Web\Controllers;


use Aigisu\Components\Auth\SessionAuth;
use Aigisu\Models\User;
use Slim\Http\Request;
use Slim\Http\Response;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionSignin(Request $request, Response $response) : Response
    {
        $auth = new SessionAuth();
        if (!$auth->isGuest()) {
            if (($user = User::findByEmail($request->getParam('email'))) &&
                $user->validatePassword($request->getParam('password'))
            ) {
                $auth->signIn($user->getKey());
            } else {
                //@todo add alert: bad authorize and go back to form
            }
        } else {
            //@todo add alert: you was logged
        }

        return $response->withRedirect('/');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param Response $response
     * @return Response
     */
    public function actionSignout(Request $request, Response $response) : Response
    {
        $auth = new SessionAuth();
        if (!$auth->isGuest()) {
            $auth->singOut();
        }

        return $response->withRedirect('/');
    }
}
