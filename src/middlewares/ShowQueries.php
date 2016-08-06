<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-06
 * Time: 02:57
 */

namespace Middlewares;


use Aigisu\Middleware;
use Slim\Http\Request;
use Slim\Http\Response;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

class ShowQueries extends Middleware
{

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $this->connection->enableQueryLog();
        /** @var $response Response */
        $response = $next($request, $response);

        return $response->withBody($this->getBodyWithNewContent($this->getQueryContent(), $response));
    }

    private function getBodyWithNewContent($newContent, Response $response)
    {
        $body = $response->getBody();
        $body->rewind();
        $content   = $body->getContents();
        $endOfBody = strpos($content, '</body>');
        $body->seek($endOfBody !== false ? $endOfBody : strlen($content));
        $body->write($newContent);

        return $body;
    }

    private function getQueryContent()
    {
        $output = fopen('php://memory', 'r+b');
        $dumper = new HtmlDumper();
        $dumper->dump((new VarCloner())->cloneVar($this->connection->getQueryLog()), $output);

        return stream_get_contents($output, -1, 0);
    }
}