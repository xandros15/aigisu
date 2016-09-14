<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-14
 * Time: 00:39
 */

namespace Aigisu\Api\Middlewares;


use Aigisu\Api\Models\Events\IconUploadListener;
use Aigisu\Api\Models\Events\UnitTagsListener;
use Aigisu\Api\Models\Unit;
use Aigisu\Components\Http\Filesystem\FilesystemManager;
use Aigisu\Components\Url\UrlManager;
use Slim\Http\Request;
use Slim\Http\Response;

class UnitEventMiddleware extends Middleware
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
        }

        $this->ready();

        return $next($request, $response);
    }

    /**
     * @param Request $request
     */
    private function applyEvents(Request $request)
    {
        Unit::saving(new IconUploadListener($request, $this->get(FilesystemManager::class)));
        Unit::saved(new UnitTagsListener());
        Unit::setUrlManager(new UrlManager($this->get('router'), $this->get('siteUrl')));
    }

    public function ready()
    {
        $this->isEventsReady = true;
    }
}