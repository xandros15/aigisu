<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-19
 * Time: 23:36
 */

namespace Aigisu\Api\Controllers;


use Aigisu\Api\Exceptions\InvalidRecoveryHashException;
use Aigisu\Api\Transformers\UserTransformer;
use Aigisu\Components\Http\Exceptions\BadRequestException;
use Aigisu\Components\Http\Exceptions\ForbiddenException;
use Aigisu\Components\Http\Exceptions\RuntimeException;
use Aigisu\Components\Mailer;
use Aigisu\Components\Serializers\SimplyArraySerializer;
use Aigisu\Core\Model;
use Aigisu\Models\User;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Slim\Http\Request;
use Slim\Http\Response;

class UserController extends AbstractController
{
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionIndex(Request $request, Response $response): Response
    {
        return $this->read($response, $this->transformUser(User::all()));
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionView(Request $request, Response $response): Response
    {
        return $this->read($response, $this->transformUser($this->findUserOrFail($request)));
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionActivate(Request $request, Response $response): Response
    {
        $this->findUserOrFail($request)->activate();

        return $this->update($response);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionChangeRole(Request $request, Response $response): Response
    {
        $user = $this->findUserOrFail($request);
        $user->changeRole($request->getParam('role'));

        return $this->update($response, $this->transformUser($user));
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionDeactivate(Request $request, Response $response): Response
    {
        $user = $this->findUserOrFail($request);
        $user->deactivate();

        return $this->update($response, $this->transformUser($user));
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionUpdate(Request $request, Response $response): Response
    {
        $user = $this->findUserOrFail($request)->fill($request->getParams());
        $user->saveOrFail();

        return $this->update($response, $this->transformUser($user));
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function actionRegister(Request $request, Response $response): Response
    {
        $user = new User($request->getParams());
        $user->setPassword($request->getParam('password'));
        $user->saveOrFail();
        $location = $this->get('router')->pathFor('api.user.view', ['id' => $user->getKey()]);

        return $this->create($response, $location, $this->transformUser($user));
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws BadRequestException
     */
    public function actionResetPasswordRequest(Request $request, Response $response): Response
    {
        $user = User::findByEmail($request->getParam('email', ''));
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
     *
     * @return Response
     * @throws InvalidRecoveryHashException
     */
    public function actionResetPassword(Request $request, Response $response): Response
    {
        $hash = $request->getAttribute('token', '');
        if (!($user = User::findByRecoveryHash($hash)) || !User::isValidRecoveryHash($hash)) {
            throw new InvalidRecoveryHashException($request, $response);
        }

        $user->changePassword($request->getParam('password'));

        return $this->update($response);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws \Exception
     */
    public function actionDelete(Request $request, Response $response): Response
    {
        $this->findUserOrFail($request)->delete();

        return $this->delete($response);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws ForbiddenException
     */
    public function actionGetCurrent(Request $request, Response $response): Response
    {
        if ($request->getAttribute('is_guest')) {
            throw new ForbiddenException($request, $response);
        }

        return $this->read($response, $this->transformUser($request->getAttribute('user')));
    }

    /**
     * @param Request $request
     *
     * @throws NotFoundException
     * @return User|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    private function findUserOrFail(Request $request)
    {
        return User::findOrFail($this->getID($request));
    }

    /**
     * @param \Traversable|array|Model $data
     *
     * @return array
     */
    private function transformUser($data)
    {
        $fractal = new Manager();
        $transformer = new UserTransformer();

        if ($data instanceof \Traversable || is_array($data)) {
            $data = new Collection($data, $transformer);
        } else {
            $data = new Item($data, $transformer);
        }

        return $fractal->setSerializer(new SimplyArraySerializer())
                       ->createData($data)
                       ->toArray();
    }
}
