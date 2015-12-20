<?php

namespace traits;

use Illuminate\Support\MessageBag;
use Main;

trait Validator
{
    /** @var  MessageBag */
    public $errors;

    public function validate($data)
    {
        if (!method_exists($this, 'rules')) {
            return true;
        }
        $validator = Main::$app->connection->validator->make($data, $this->rules());

        $this->errors = $validator->errors();
        return $validator->passes();
    }

    public function getErrors()
    {
        return $this->errors;
    }
}