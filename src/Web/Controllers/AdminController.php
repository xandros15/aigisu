<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-03
 * Time: 15:35
 */

namespace Aigisu\Web\Controllers;


use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

class AdminController extends AbstractController
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionIndexUsers(Request $request, Response $response): Response
    {
        $api = $this->callApi('api.user.index', $request, $response);
        $users = $api->getResponse();

        return $this->get(Twig::class)->render($response, 'admin/users.twig', [
            'users' => $users
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionActivateUser(Request $request, Response $response): Response
    {
        $this->callApi('api.user.activate', $request, $response);
        return $response->withRedirect('/admin/users');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionDeactivateUser(Request $request, Response $response): Response
    {
        $this->callApi('api.user.deactivate', $request, $response);
        return $response->withRedirect('/admin/users');
    }
}
