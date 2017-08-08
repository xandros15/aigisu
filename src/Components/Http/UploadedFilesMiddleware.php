<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-12
 * Time: 02:11
 */

namespace Aigisu\Components\Http;


use Aigisu\Core\ActiveContainer;
use Aigisu\Core\MiddlewareInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class UploadedFilesMiddleware extends ActiveContainer implements MiddlewareInterface
{

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        $request = $this->withUploadedFiles($request);

        return $next($request, $response);
    }

    private function getFilesystem(): Filesystem
    {
        return $this->get(Filesystem::class);
    }

    /**
     * @param Request $request
     *
     * @return Request
     */
    private function withUploadedFiles(Request $request): Request
    {
        $newFiles = UploadedFile::createFromEnvironment(new FakeEnvironment());
        $this->attachManagerToFiles($newFiles, $this->getFilesystem());

        return $request->withUploadedFiles($newFiles);
    }

    private function attachManagerToFiles(array $newFiles, FilesystemInterface $filesystem)
    {
        foreach ($newFiles as $file) {
            if (is_array($file)) {
                $this->attachManagerToFiles($file, $filesystem);
            } elseif ($file instanceof UploadedFile) {
                $file->addManager($filesystem);
            }
        }
    }
}
