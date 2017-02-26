<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-11
 * Time: 20:00
 */

namespace Aigisu\Api\Middlewares;


use Aigisu\Components\Http\Base64UploadedFile;
use Aigisu\Core\MiddlewareInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class Base64FileMiddleware implements MiddlewareInterface
{

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        if (!is_null($filesParam = $request->getParam('files')) && $files = $this->createFilesFromParam($filesParam)) {
            $request = $request->withUploadedFiles($files);
        }

        return $next($request, $response);
    }

    /**
     * @param array $files
     * @return array
     */
    private function createFilesFromParam(array $files) : array
    {
        $filesToUpload = [];
        foreach ($files as $name => $value) {
            $filesToUpload[$name] = new Base64UploadedFile([
                'name' => $name,
                'value' => $value,
            ]);
        }

        return $filesToUpload;
    }
}
