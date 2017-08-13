<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-03
 * Time: 15:35
 */

namespace Aigisu\Web\Controllers;


use Aigisu\Components\Form;
use Aigisu\Web\Components\MultipartStream;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

class AdminController extends AbstractController
{
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionIndexUsers(Request $request, Response $response): Response
    {
        $api = $this->api->request('/users');
        $users = $api->getArrayBody();

        return $this->get(Twig::class)->render($response, 'admin/users.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionActivateUser(Request $request, Response $response): Response
    {
        $id = $request->getAttribute('id');
        $this->api->request('/users/' . $id . '/active', 'POST');

        return $response->withRedirect('/admin/users');
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionDeactivateUser(Request $request, Response $response): Response
    {
        $id = $request->getAttribute('id');
        $this->api->request('/users/' . $id . '/deactivate', 'POST');

        return $response->withRedirect('/admin/users');
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionUpdateUser(Request $request, Response $response): Response
    {
        $id = $request->getAttribute('id');
        $user = $this->api->request('/users/' . $id)->getArrayBody();
        if ($request->isPost()) {
            $api = $this->api->request('/users/' . $id, 'POST', new MultipartStream($request));
            if ($api->hasError()) {
                $request = $request->withAttribute('errors', $api->getErrors());
            } else {
                $this->flash->addSuccess('Successful update user.');

                return $response->withRedirect('/admin/users');
            }
        }

        $form = new Form($request);

        return $this->get(Twig::class)->render($response, 'admin/user-form.twig', [
            'form' => $form->all(),
            'user' => $user,
        ]);
    }
}
