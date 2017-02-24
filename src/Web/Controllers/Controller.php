<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-11-19
 * Time: 16:31
 */

namespace Aigisu\Web\Controllers;


use Aigisu\Core\ActiveContainer;
use Slim\Http\Response;

abstract class Controller extends ActiveContainer
{

    const HOME_PATH_NAME = 'web.home';

    /**
     * @param Response $response
     * @return Response
     */
    public function goHome(Response $response) : Response
    {
        $path = $this->get('router')->pathFor(self::HOME_PATH_NAME);
        return $response->withRedirect($path);
    }

}
