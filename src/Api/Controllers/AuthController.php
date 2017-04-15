<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-06
 * Time: 00:11
 */

namespace Aigisu\Api\Controllers;


use Aigisu\Components\Auth\JWTAuth;
use Aigisu\Components\Http\Exceptions\UnauthorizedException;
use Aigisu\Models\User;
use Slim\Http\Request;
use Slim\Http\Response;

class AuthController extends AbstractController
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws UnauthorizedException
     */
    public function actionCreate(Request $request, Response $response) : Response
    {
        $user = User::findByEmail($request->getParam('email'));
        if (!$user || !$user->validatePassword($request->getParam('password', ''))) {
            throw new UnauthorizedException($request, $response);
        }

        $auth  = new JWTAuth($this->get('settings')->get('auth'));
        $token = $auth->createToken($user->getKey());
        return $this->read($response, [
            'token' => (string)$token,
            'expires_at' => $token->getClaim('exp'),
            'token_type' => "Bearer",
        ]);
    }
}
