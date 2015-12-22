<?php

namespace traits;

use Illuminate\Support\MessageBag;
use Illuminate\Database\Eloquent\Model;
use app\alert\Alert;
use Main;

trait Validator
{
    /** @var  MessageBag */
    public $errors;

    public function validate()
    {
        /* @var $this Model */
        if (!method_exists($this, 'rules')) {
            return true;
        }

        if (method_exists($this, 'beforeValidate')) {
            $this->beforeValidate();
        }

        $validator = Main::$app->connection->validator->make($this->getAttributes(), $this->rules());

        $result = $validator->passes();

        if (method_exists($this, 'afterValidate')) {
            $this->beforeValidate();
        }

        if (!$result) {
            foreach ($validator->errors()->getMessages() as $errors) {
                foreach ($errors as $error) {
                    Alert::add($error, Alert::ERROR);
                }
            }
            return false;
        }
        return true;
    }
}