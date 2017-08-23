<?php


namespace Aigisu\Web\Components;


use Aigisu\Components\Http\Exceptions\ForbiddenException;
use Aigisu\Components\Http\Exceptions\RuntimeException;
use Aigisu\Components\Http\Exceptions\UnauthorizedException;
use Aigisu\Core\MiddlewareInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class ApiHandlerMiddleware implements MiddlewareInterface
{
    private const SERVER_DEFAULT_ERROR_CODE = 500;

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     *
     * @return Response
     * @throws RuntimeException
     * @throws NotFoundException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     */
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        try {
            return $next($request, $response);
        } catch (ServerException $exception) {
            throw new RuntimeException('Api server exception: ' . $exception->getResponse()->getReasonPhrase(),
                self::SERVER_DEFAULT_ERROR_CODE);
        } catch (ClientException $exception) {
            $apiResponse = $exception->getResponse();
            switch ($apiResponse->getStatusCode()) {
                case 404:
                    throw new NotFoundException($request, $response);
                case 401:
                    throw new UnauthorizedException($request, $response);
                case 403:
                    throw new ForbiddenException($request, $response);
                default:
                    throw new RuntimeException('Api server exception:' . $apiResponse->getReasonPhrase(),
                        $apiResponse->getStatusCode());
            }
        }
    }
}
