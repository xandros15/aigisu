<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-06
 * Time: 00:11
 */

namespace Aigisu\Api\Controllers;


use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Slim\Http\Request;
use Slim\Http\Response;

class AuthController extends AbstractController
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionCreate(Request $request, Response $response) : Response
    {
        /* @var \League\OAuth2\Server\AuthorizationServer $server */
        $server = $this->get(AuthorizationServer::class);
        $request = $this->prepareParams($request);
        try {
            // Try to respond to the access token request
            return $server->respondToAccessTokenRequest($request, $response);
        } catch (OAuthServerException $exception) {
            // All instances of OAuthServerException can be converted to a PSR-7 response
            return $exception->generateHttpResponse($response);
        } catch (\Exception $exception) {
            // Catch unexpected exceptions
            $body = $response->getBody();
            $body->write($exception->getMessage());
            return $response->withStatus(500)->withBody($body);
        }
    }

    /**
     * @param Request $request
     * @return Request
     */
    protected function prepareParams(Request $request) : Request
    {
        $parsedBody = $request->getParsedBody();
        if (!isset($parsedBody['client_id']) && isset($parsedBody['username'])) {
            $parsedBody['client_id'] = $parsedBody['username'];
        }
        
        return $request->withParsedBody($parsedBody);
    }
}
