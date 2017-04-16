<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-09
 * Time: 15:54
 */

namespace Aigisu\Components\ACL;


use Aigisu\Components\Http\Exceptions\ForbiddenException;
use Aigisu\Core\ActiveContainer;
use Aigisu\Core\MiddlewareInterface;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class AbstractAccessMiddleware extends ActiveContainer implements MiddlewareInterface
{
    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     *
     * @return Response
     * @throws ForbiddenException
     */
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        if (!$this->hasAccess($request)) {
            throw new ForbiddenException($request, $response);
        }

        $response = $next($request, $response);

        return $response;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected abstract function hasAccess(Request $request): bool;

    /**
     * @param string $role
     * @param string $class
     *
     * @return bool
     */
    protected function compareAccess(string $role, string $class): bool
    {
        return $this->getLvlByRole($role) <= $this->getLvlByClass($class);
    }

    /**
     * @return array
     */
    protected function getAccessList(): array
    {
        return $this->get('settings')->get('access');
    }

    /**
     * @param string $role
     *
     * @return int
     */
    private function getLvlByRole(string $role): int
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
     *
     * @return int
     */
    private function getLvlByClass(string $class): int
    {
        foreach ($this->getAccessList() as $access) {
            if ($access['class'] === $class) {
                return $access['level'];
            }
        }

        throw new \RuntimeException('Access class not found');
    }
}
