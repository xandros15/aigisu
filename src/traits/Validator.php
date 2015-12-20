<?php

namespace traits;

use Illuminate\Validation\Factory as ValidatorFactory;
use Illuminate\Support\MessageBag;
use Illuminate\Translation\Translator;
use Illuminate\Translation\FileLoader;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Validation\ValidationServiceProvider;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Validation\DatabasePresenceVerifier;
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

        // make a new validator object
        $filesystem = new FileLoader(new Filesystem(), __DIR__ . DIRECTORY_SEPARATOR . 'langs');
        $translator = new Translator($filesystem, Main::$app->web->get('locale', false) ? : 'en');
        $validator  = (new ValidatorFactory($translator))->make($data, $this->rules());
        // return the result

        $validator->setPresenceVerifier(new DatabasePresenceVerifier(static::$resolver));

        $this->errors = $validator->errors();
        return $validator->passes();
    }

    public function getErrors()
    {
        return $this->errors;
    }
}