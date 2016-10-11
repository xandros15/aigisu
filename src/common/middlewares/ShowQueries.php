<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-06
 * Time: 02:57
 */

namespace Aigisu\Common\Middlewares;


use Aigisu\Common\Components\View\LayoutExtension;
use Aigisu\Common\Components\View\View;
use Aigisu\Core\Middleware;
use Illuminate\Database\Connection;
use Slim\Http\Request;
use Slim\Http\Response;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

/**
 * @property LayoutExtension $view
 */
class ShowQueries extends Middleware
{

    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        if ($this->get('isDebug')) {
            /** @var $connection Connection */
            $connection = $this->get(Connection::class);
            $connection->enableQueryLog();
            $this->get(View::class)->append(function () use ($connection) {
                $queries = $this->getQueryContent($connection);
                return $queries ? $queries : '';
            }, LayoutExtension::PH_BODY_END, 1);
        }
        return $next($request, $response);
    }

    private function getQueryContent(Connection $connection) : string
    {
        $queries = $connection->getQueryLog();

        $output = '';

        if ($queries) {
            $output = fopen('php://memory', 'r+b');
            $dumper = new HtmlDumper();
            $dumper->dump((new VarCloner())->cloneVar($queries), $output);
            $output = stream_get_contents($output, -1, 0);
        }

        return $output;
    }
}