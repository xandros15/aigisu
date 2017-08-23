<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-06
 * Time: 00:11
 */

namespace Aigisu\Api\Controllers;


use Aigisu\Components\Auth\JWTAuth;
use Aigisu\Components\Http\Exceptions\BadRequestException;
use Aigisu\Components\Http\Exceptions\UnauthorizedException;
use Aigisu\Models\User;
use Slim\Http\Request;
use Slim\Http\Response;

class AuthController extends AbstractController
{
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws BadRequestException
     * @throws UnauthorizedException
     */
    public function actionCreate(Request $request, Response $response): Response
    {
        switch ($request->getParam('grant_type', '')) {
            case 'password':
                $user = User::findByEmail($request->getParam('email', ''));
                if (!$user || !$user->validatePassword($request->getParam('password', ''))) {
                    throw new UnauthorizedException($request, $response);
                }
                $user->generateRefreshToken();
                $payload = ['refresh_token' => $user->getAttribute('refresh_token')];
                break;
            case 'refresh_token':
                $user = User::findByRefreshToken($request->getParam('refresh_token', ''));
                if (!$user) {
                    throw new UnauthorizedException($request, $response);
                }
                $payload = [];
                break;
            default:
                throw new BadRequestException($request, $response);
        }

        $auth = new JWTAuth($this->get('settings')->get('auth'));
        $token = $auth->createToken($user->getKey());
        $payload = $payload + [
                'user_id' => $user->getKey(),
                'access_token' => (string) $token,
                'expires_at' => $token->getClaim('exp'),
                'token_type' => "Bearer",
            ];

        return $this->read($response, $payload);
    }
}
