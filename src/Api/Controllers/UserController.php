<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-19
 * Time: 23:36
 */

namespace Aigisu\Api\Controllers;


use Aigisu\Api\Exceptions\InvalidRecoveryHashException;
use Aigisu\Components\Http\Exceptions\BadRequestException;
use Aigisu\Components\Http\Exceptions\ForbiddenException;
use Aigisu\Components\Http\Exceptions\RuntimeException;
use Aigisu\Components\Mailer;
use Aigisu\Models\User;
use Slim\Http\Request;
use Slim\Http\Response;

class UserController extends AbstractController
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionIndex(Request $request, Response $response) : Response
    {
        return $this->read($response, User::all()->toArray());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionView(Request $request, Response $response) : Response
    {
        $user = $this->findUserOrFail($request);
        return $this->read($response, $user->toArray());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionActivate(Request $request, Response $response) : Response
    {
        $this->findUserOrFail($request)->activate();
        return $this->update($response);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionChangeRole(Request $request, Response $response) : Response
    {
        $this->findUserOrFail($request)->changeRole($request->getParam('role'));
        return $this->update($response);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionDeactivate(Request $request, Response $response) : Response
    {
        $this->findUserOrFail($request)->deactivate();
        return $this->update($response);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionUpdate(Request $request, Response $response) : Response
    {
        $this->findUserOrFail($request)->fill($request->getParams())->saveOrFail();
        return $this->update($response);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function actionRegister(Request $request, Response $response) : Response
    {
        $user = new User($request->getParams());
        $user->setPassword($request->getParam('password'));
        $user->saveOrFail();

        return $this->create($response, $this->get('router')->pathFor('api.user.view', ['id' => $user->getKey()]));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws BadRequestException
     */
    public function actionResetPasswordRequest(Request $request, Response $response) : Response
    {
        if (!$user = User::findByEmail($request->getParam('email'))) {
            $response = $response->withJson(['message' => ['email' => 'email not found']]);
            throw new BadRequestException($request, $response);
        }

        $user->generateRecoveryHash();

        $isSend = $this->get(Mailer::class)->send([
            'to' => $user->email,
            'subject' => 'Reset Password',
            'view' => 'mail/reset-password.twig',
            'user' => $user,
        ]);

        if (!$isSend) {
            throw new RuntimeException('Can\'t send email');
        }

        return $this->update($response);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws InvalidRecoveryHashException
     */
    public function actionResetPassword(Request $request, Response $response) : Response
    {
        $hash = $request->getAttribute('token');
        if (!User::isValidRecoveryHash($hash) || !$user = User::findByRecoveryHash($hash)) {
            throw new InvalidRecoveryHashException($request, $response);
        }

        $user->changePassword($request->getParam('password'));

        return $this->update($response);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function actionDelete(Request $request, Response $response) : Response
    {
        $this->findUserOrFail($request)->delete();
        return $this->delete($response);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws ForbiddenException
     */
    public function actionGetCurrent(Request $request, Response $response) : Response
    {
        if ($request->getAttribute('is_guest')) {
            throw new ForbiddenException($request, $response);
        }

        return $this->read($response, $request->getAttribute('user')->toArray());
    }

    /**
     * @param Request $request
     * @throws NotFoundException
     * @return User|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    private function findUserOrFail(Request $request)
    {
        return User::findOrFail($this->getID($request));
    }
}
