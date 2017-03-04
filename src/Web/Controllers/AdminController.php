<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-03
 * Time: 15:35
 */

namespace Aigisu\Web\Controllers;


use Aigisu\Components\Form;
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

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionViewUser(Request $request, Response $response): Response
    {
        $user = $this->callApi('api.user.view', $request, $response)->getResponse();
        $form = new Form($request);
        $form['form'] = array_merge($user, $form['form']);

        return $this->get(Twig::class)->render($response, 'admin/user-form.twig', $form->all());
    }


    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionUpdateUser(Request $request, Response $response): Response
    {

        $api = $this->callApi('api.user.update', $request, $response);
        if ($api->hasError()) {
            $request = $request->withAttribute('errors', $api->getErrors());
            return $this->actionViewUser($request, $response);
        }

        $this->flash->addSuccess('Successful update user.');

        return $response->withRedirect('/admin/users');
    }
}
