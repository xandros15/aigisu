<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-24
 * Time: 10:31
 */

namespace Aigisu\Middlewares;


use Aigisu\Components\Http\BadRequestException;
use Aigisu\Components\Http\UploadedFile;
use Aigisu\Components\Validators\ValidatorInterface;
use Aigisu\Components\Validators\ValidatorManager;
use Aigisu\Core\ActiveContainer;
use Aigisu\Core\MiddlewareInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class ValidatorMiddleware extends ActiveContainer implements MiddlewareInterface
{
    const MESSAGE = 'message';

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     * @throws BadRequestException
     */
    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        $validator = $this->createValidator($request);
        if ($validator && !$validator->validate($this->getParams($request), $this->getContext($request))) {
            $response = $response->withJson([
                self::MESSAGE => $validator->getErrors(),
            ]);

            throw new BadRequestException($request, $response);
        }

        return $next($request, $response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    private function getContext(Request $request)
    {
        return $request->getAttribute('route')->getArguments();
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getParams(Request $request) : array
    {
        return array_merge($request->getParams(), $this->parseUploadedFiles($request->getUploadedFiles()));
    }

    /**
     * @param UploadedFile[] $uploadedFiles
     * @return array
     */
    private function parseUploadedFiles(array $uploadedFiles) : array
    {
        $files = [];
        foreach ($uploadedFiles as $name => $file) {
            $files[$name] = $file->exist() ? new \SplFileObject($file->file) : null;
        }

        return $files;
    }

    /**
     * @param Request $request
     * @return ValidatorInterface|null
     */
    private function createValidator(Request $request)
    {
        if (!$route = $request->getAttribute('route')) {
            return null;
        }

        $validator = (string)$route->getArgument('validator', '');

        return $this->get(ValidatorManager::class)->get($validator);
    }
}
