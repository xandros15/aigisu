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
use Slim\Http\Request;
use Slim\Http\Response;

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
        return $this->render($request, $response, 'site/signup.twig', new Form($request));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws BadRequestException
     */
    public function actionSignin(Request $request, Response $response): Response
    {
        $auth = new SessionAuth();
        if (!$auth->isGuest()) {
            throw new NotFoundException($request, $response);
        }
        if (!$auth->signIn($request->getParam('email', ''), $request->getParam('password', ''))) {
            $this->flash->addError('Wrong username or email', true);
            return $this->actionView($request, $response);
        }

        return $this->goHome($response);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @throws BadRequestException
     * @return Response
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
        return $this->render($request, $response, 'site/signin.twig', [
            'form' => $request->getParams(),
        ]);
    }

}
