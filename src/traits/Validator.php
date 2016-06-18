<?php

namespace traits;

use Illuminate\Container\Container;
use Illuminate\Support\MessageBag;
use Illuminate\Database\Eloquent\Model;
use app\alert\Alert;

trait Validator
{
    /** @var MessageBag */
    public $errors;

    public function validate($attributes = [])
    {
        /* @var $this Model */
        if (!method_exists($this, 'rules')) {
            return true;
        }

        $attributes = ($attributes) ? array_merge($this->getAttributes(), $attributes) : $this->getAttributes();
        /** @var $validator \Illuminate\Validation\Validator */
        $validator = Container::getInstance()->validator->make($attributes, $this->rules());

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