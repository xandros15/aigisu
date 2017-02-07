<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-09
 * Time: 15:54
 */

namespace Aigisu\Api\Middlewares\Access;


use Aigisu\Api\Messages;
use Aigisu\Api\Middlewares\Middleware;
use Aigisu\Components\Http\ForbiddenException;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class AbstractAccessMiddleware extends Middleware implements Messages
{
    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     * @throws ForbiddenException
     */
    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        if (!$this->hasAccess($request)) {
            throw new ForbiddenException($request, $response);
        }

        $response = $next($request, $response);
        return $response;
    }

    /**
     * @param Request $request
     * @return bool
     */
    protected abstract function hasAccess(Request $request) : bool;

    /**
     * @param string $role
     * @param string $class
     * @return bool
     */
    protected function compareAccess(string $role, string $class) : bool
    {
        return $this->getLvlByRole($role) <= $this->getLvlByClass($class);
    }

    /**
     * @return array
     */
    protected function getAccessList() : array
    {
        return $this->get('access');
    }

    /**
     * @param string $role
     * @return int
     */
    private function getLvlByRole(string $role) : int
    {
        foreach ($this->getAccessList() as $access) {
            if (strtoupper($access['role']) === strtoupper($role)) {
                return $access['level'];
            }
        }

        throw new \RuntimeException('Access role not found');
    }

    /**
     * @param string $class
     * @return int
     */
    private function getLvlByClass(string $class) : int
    {
        foreach ($this->getAccessList() as $access) {
            if ($access['class'] === $class) {
                return $access['level'];
            }
        }

        throw new \RuntimeException('Access class not found');
    }
}
