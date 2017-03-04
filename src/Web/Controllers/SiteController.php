<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-01
 * Time: 21:30
 */

namespace Aigisu\Web\Controllers;


use Aigisu\Components\Auth\SessionAuth;
use Aigisu\Components\Form;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

class SiteController extends AbstractController
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionRegister(Request $request, Response $response): Response
    {
        $api = $this->callApi('api.user.create', $request, $response);
        if ($api->hasError()) {
            $request = $request->withAttribute('errors', $api->getErrors());
            return $this->actionRegisterView($request, $response);
        }

        $this->flash->addSuccess('Successful sign up. Now you need to wait for accept your account by admin.');

        return $this->goHome($response);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionRegisterView(Request $request, Response $response): Response
    {
        return $this->get(Twig::class)->render($response, 'site/signup.twig', [
            'form' => (new Form($request))->all(),
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws NotFoundException
     */
    public function actionSignin(Request $request, Response $response): Response
    {
        $auth = new SessionAuth();
        if (!$auth->isGuest()) {
            throw new NotFoundException($request, $response);
        }
        if (!$auth->signIn($request->getParam('email', ''), $request->getParam('password', ''))) {
            $this->flash->addError('Wrong email or password', true);
            return $this->actionView($request, $response);
        }

        return $this->goHome($response);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws NotFoundException
     */
    public function actionSignout(Request $request, Response $response): Response
    {
        $auth = new SessionAuth();
        if ($auth->isGuest()) {
            throw new NotFoundException($request, $response);
        }
        $auth->singOut();

        return $this->goHome($response);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionView(Request $request, Response $response): Response
    {
        return $this->get(Twig::class)->render($response, 'site/signin.twig', [
            'form' => (new Form($request))->all(),
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionPasswordReset(Request $request, Response $response): Response
    {
        $form = new Form($request);
        if ($request->isPost()) {
            $request = $request->withAttribute('token', $request->getParam('token', ''));
            $api = $this->callApi('api.user.password.reset', $request, $response);
            if ($api->hasError()) {
                $request = $request->withAttribute('errors', $api->getErrors());
                $form = $form->withRequest($request);
            } else {
                $this->flash->addSuccess('Successful change password');
                return $this->goHome($response);
            }
        }

        return $this->get(Twig::class)->render($response, 'site/password-reset.twig', $form->all());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionPasswordResetRequest(Request $request, Response $response): Response
    {
        $form = new Form($request);
        if ($request->isPost()) {
            $api = $this->callApi('api.user.password.reset.send', $request, $response);
            if ($api->hasError()) {
                $request = $request->withAttribute('errors', $api->getErrors());
                $form = $form->withRequest($request);
            } else {
                return $response->withRedirect('/password/reset');
            }
        }

        return $this->get(Twig::class)->render($response, 'site/password-reset-request.twig', $form->all());
    }

}
