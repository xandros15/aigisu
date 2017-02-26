<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-24
 * Time: 10:31
 */

namespace Aigisu\Api\Middlewares;


use Aigisu\Components\Http\BadRequestException;
use Aigisu\Components\Validators\ValidatorInterface;
use Aigisu\Core\MiddlewareInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class ValidatorMiddleware implements MiddlewareInterface
{
    const MESSAGE = 'message';

    /** @var ValidatorInterface */
    private $validator;

    /**
     * ValidatorMiddleware constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     * @throws BadRequestException
     */
    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        if (!$this->validator->validate($request->getParams()) ||
            !$this->validator->validateFiles($request->getUploadedFiles())
        ) {
            $response = $response->withJson([
                self::MESSAGE => $this->validator->getErrors(),
            ]);

            throw new BadRequestException($request, $response);
        }

        return $next($request, $response);
    }
}
