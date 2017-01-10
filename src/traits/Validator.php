<?php

namespace traits;

use app\alert\Alert;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\MessageBag;
use Main;

trait Validator
{
    /** @var  MessageBag */
    public $errors;

    public function validate($attributes = [])
    {
        /* @var $this Model */
        if (!method_exists($this, 'rules')) {
            return true;
        }

        $attributes = ($attributes) ? array_merge($this->getAttributes(), $attributes) : $this->getAttributes();

        $validator = Main::$app->connection->validator->make($attributes, $this->rules());

        $result = $validator->passes();

        if (!$result) {
            $this->errors = $validator->errors();
            foreach ($this->errors->getMessages() as $errors) {
                foreach ($errors as $error) {
                    Alert::add($error, Alert::ERROR);
                }
            }
            return false;
        }
        return true;
    }
}