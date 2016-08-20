<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-19
 * Time: 23:36
 */

namespace Controllers;


use Aigisu\Controller;

class UserController extends Controller
{
    public function actionIndex()
    {
        return $this->render('user/index');
    }

    public function actionView()
    {
        return $this->render('user/view');
    }

    public function actionCreate()
    {
        if ($this->request->isPost()) {
            //@todo redirect to created user
            return $this->response->withRedirect('/users');
        }

        return $this->render('/user/create');
    }

    public function actionUpdate()
    {
        if ($this->request->isPost()) {
            //@todo redirect to updated user
            return $this->response->withRedirect('/users');
        }

        return $this->render('/user/edit');
    }

    public function actionDelete()
    {
        return $this->response->withRedirect('/users');
    }
}