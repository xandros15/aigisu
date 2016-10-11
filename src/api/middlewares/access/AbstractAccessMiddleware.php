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
use Aigisu\Api\Models\User;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class AbstractAccessMiddleware extends Middleware implements Messages
{
    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        $request = $this->addUserToRequest($request);
        if ($this->hasAccess($request)) {
            $response = $next($request, $response);
        } else {
            $response = $response->withStatus(self::STATUS_FORBIDDEN);
        }

        return $response;
    }

    /**
     * @param Request $request
     * @return bool
     */
    protected abstract function hasAccess(Request $request) : bool;

    /**
     * @param Request $request
     * @return Request
     */
    protected function addUserToRequest(Request $request) : Request
    {
        $user = null;
        $isGuest = true;

        if ($id = $request->getAttribute('oauth_user_id')) {
            if ($user = User::find($id)) {
                $isGuest = false;
            }
        }

        return $request->withAttributes(array_merge($request->getAttributes(), [
            'user' => $user,
            'is_guest' => $isGuest,
        ]));
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
     * @param string $class
     * @return bool
     */
    protected function compareAccess(string $role, string $class) : bool
    {
        return $this->getLvlByRole($role) <= $this->getLvlByClass($class);
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
}