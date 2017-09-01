<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-24
 * Time: 10:31
 */

namespace Aigisu\Components\Validators;


use Aigisu\Components\Http\Exceptions\BadRequestException;
use Aigisu\Components\Http\UploadedFile;
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
     *
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
     *
     * @return Response
     * @throws BadRequestException
     */
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        if (!$this->validator->validate($this->getParams($request), $this->getContext($request))) {
            $response = $response->withJson([
                self::MESSAGE => $this->validator->getErrors(),
            ]);

            throw new BadRequestException($request, $response);
        }

        return $next($request, $response);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    private function getContext(Request $request)
    {
        return $request->getAttribute('route')->getArguments();
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function getParams(Request $request): array
    {
        return array_merge($request->getParams(), $this->parseUploadedFiles($request->getUploadedFiles()));
    }

    /**
     * @param UploadedFile[] $uploadedFiles
     *
     * @return array
     */
    private function parseUploadedFiles(array $uploadedFiles): array
    {
        $files = [];
        foreach ($uploadedFiles as $name => $file) {
            $files[$name] = $file->exist() ? new \SplFileObject($file->file) : null;
        }

        return $files;
    }
}
