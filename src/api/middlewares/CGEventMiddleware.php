<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-14
 * Time: 00:39
 */

namespace Aigisu\Api\Middlewares;


use Aigisu\Api\Models\Events\CGUploadListener;
use Aigisu\Api\Models\Unit;
use Aigisu\Api\Models\Unit\CG;
use Aigisu\Components\Http\Filesystem\FilesystemManager;
use Slim\Http\Request;
use Slim\Http\Response;

class CGEventMiddleware extends Middleware
{
    /** @var bool */
    private $isEventsReady = false;

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        if (!$this->isEventsReady) {
            $this->applyEvents($request);
            $this->ready();
        }

        return $next($request, $response);
    }

    /**
     * @param Request $request
     */
    private function applyEvents(Request $request)
    {
        CG::saving(new CGUploadListener($request, $this->get(FilesystemManager::class)));
    }

    public function ready()
    {
        $this->isEventsReady = true;
    }
}