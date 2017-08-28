<?php


namespace Aigisu\Web\Components;


use Aigisu\Components\Http\Exceptions\ApiException;
use Aigisu\Components\Http\Exceptions\ForbiddenException;
use Aigisu\Components\Http\Exceptions\RuntimeException;
use Aigisu\Components\Http\Exceptions\UnauthorizedException;
use Aigisu\Core\MiddlewareInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class ApiHandlerMiddleware implements MiddlewareInterface
{
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
     * @throws ApiException
     */
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        try {
            return $next($request, $response);
        } catch (ServerException $exception) {
            throw $this->createApiException($exception);
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
                    throw $this->createApiException($exception);
            }
        }
    }

    /**
     * @param BadResponseException $exception
     *
     * @return ApiException|RuntimeException
     */
    private function createApiException(BadResponseException $exception)
    {
        $json = json_decode((string) $exception->getResponse()->getBody(), true);

        if (!empty($json['error'])) {
            return new ApiException(reset($json['error']));
        }

        if (!empty($json['exception'])) {
            return new ApiException(reset($json['exception']));
        }

        return new RuntimeException('Api server exception: Server exception');
    }
}
