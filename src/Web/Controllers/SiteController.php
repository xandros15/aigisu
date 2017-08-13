<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-01
 * Time: 21:30
 */

namespace Aigisu\Web\Controllers;


use Aigisu\Components\Form;
use Aigisu\Web\Components\JwtAuth;
use Aigisu\Web\Components\MultipartStream;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

class SiteController extends AbstractController
{
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionRegister(Request $request, Response $response): Response
    {
        if ($request->isPost()) {
            $api = $this->api->request('/users', 'POST', new MultipartStream($request));
            if (!$api->hasError()) {
                $this->flash->addSuccess('Successful sign up. Now you need to wait for accept your account by admin.');

                return $this->goHome($response);
            }
            $request = $request->withAttribute('errors', $api->getErrors());
        }

        $form = new Form($request);

        return $this->get(Twig::class)->render($response, 'site/signup.twig', [
            'form' => $form->all(),
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws NotFoundException
     */
    public function actionSignin(Request $request, Response $response): Response
    {
        $auth = new JwtAuth();
        if (!$auth->isGuest()) {
            throw new NotFoundException($request, $response);
        }

        if ($request->isPost()) {
            if ($this->api->auth(
                $request->getParam('email', ''),
                $request->getParam('password', ''),
                $this->get('settings')->get('auth')['public']
            )) {
                $this->flash->addSuccess('Successful login.');

                return $this->goHome($response);
            }
            $this->flash->addInstantError('Wrong email or password.');
        }

        $form = new Form($request);

        return $this->get(Twig::class)->render($response, 'site/signin.twig', [
            'form' => $form->all(),
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws NotFoundException
     */
    public function actionSignout(Request $request, Response $response): Response
    {
        $auth = new JwtAuth();
        if ($auth->isGuest()) {
            throw new NotFoundException($request, $response);
        }
        $auth->singOut();
        $this->flash->addSuccess('Successful logout.');

        return $this->goHome($response);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionPasswordReset(Request $request, Response $response): Response
    {
        if ($request->isPost()) {
            $token = $request->getParam('token', '');
            $api = $this->api->request('/users/password/reset/' . $token, 'POST', new MultipartStream($request));
            if ($api->hasError()) {
                $request = $request->withAttribute('errors', $api->getErrors());
            } else {
                $this->flash->addSuccess('Successful change password.');

                return $this->goHome($response);
            }
        }

        $form = new Form($request);

        return $this->get(Twig::class)->render($response, 'site/password-reset.twig', $form->all());
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionPasswordResetRequest(Request $request, Response $response): Response
    {
        if ($request->isPost()) {
            $api = $this->api->request('/users/password/reset/send', 'POST', new MultipartStream($request));
            if ($api->hasError()) {
                $request = $request->withAttribute('errors', $api->getErrors());
            } else {
                return $response->withRedirect('/password/reset');
            }
        }

        $form = new Form($request);

        return $this->get(Twig::class)->render($response, 'site/password-reset-request.twig', $form->all());
    }

}
