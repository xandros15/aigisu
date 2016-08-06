<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-06
 * Time: 02:57
 */

namespace Middlewares;


use Aigisu\Middleware;
use Slim\Http\Body;
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

        $output = $this->getQueryContent();

        return $output ? $response->withBody($this->getBodyWithNewContent($output, $response)) : $response;
    }

    private function getQueryContent() : string
    {
        $queries = $this->connection->getQueryLog();

        $output = '';

        if ($queries) {
            $output = fopen('php://memory', 'r+b');
            $dumper = new HtmlDumper();
            $dumper->dump((new VarCloner())->cloneVar($queries), $output);
            $output = stream_get_contents($output, -1, 0);
        }

        return $output;
    }

    private function getBodyWithNewContent($newContent, Response $response) : Body
    {
        $body = $response->getBody();
        $body->rewind();
        $content = $body->getContents();
        $endOfBody = strpos($content, '</body>');
        $body->seek($endOfBody !== false ? $endOfBody : strlen($content));
        $body->write($newContent);

        return $body;
    }
}