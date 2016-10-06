<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-06
 * Time: 00:11
 */

namespace Aigisu\Api\Controllers;


use Aigisu\Api\Models\User;
use Slim\Http\Request;
use Slim\Http\Response;

class AuthController extends Controller
{
    public function actionCreate(Request $request, Response $response) : Response
    {
        $login = $request->getParam('login');
        $password = $request->getParam('password');
        if (filter_var($login, FILTER_VALIDATE_EMAIL) !== false) {
            $user = User::where(['email' => $login])->get()->first();
        } else {
            $user = User::where(['name' => $login])->get()->first();
        }

        /** @var $user User */
        if ($user && password_verify($password, $user->password)) {
            $response = $response->withJson($user->generateNewToken())->withStatus(self::STATUS_OK);
        } else {
            $response = $response->withStatus(self::STATUS_BAD_REQUEST);
        }

        return $response;
    }

    public function actionDelete(Request $request, Response $response) : Response
    {

    }

    private function logout(User $user, Response $response)
    {

    }
}