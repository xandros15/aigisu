<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-04
 * Time: 21:24
 */

namespace Aigisu\Components;


use Aigisu\Core\ActiveContainer;
use Aigisu\Core\MiddlewareInterface;
use Slim\Flash\Messages;
use Slim\Http\Request;
use Slim\Http\Response;

class CloneFlashMiddleware extends ActiveContainer implements MiddlewareInterface
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
        /** @var $messages Messages */
        $messages = $this->get(Messages::class);

        if ($messages->hasMessage(Flash::KEY_NAME)) {
            foreach ($messages->getMessages()[Flash::KEY_NAME] as $message) {
                $messages->addMessage(Flash::KEY_NAME, $message);
            }
        }

        return $next($request, $response);
    }
}
