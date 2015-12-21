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

        $validator = Main::$app->connection->validator->make($this->getAttributes(), $this->rules());

        if ($validator->passes()) {
            return true;
        }
        foreach ($validator->errors()->getMessages() as $errors) {
            foreach ($errors as $error) {
                Alert::add($error, Alert::ERROR);
            }
        }
        return false;
    }
}