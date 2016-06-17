<?php

namespace app\core;

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Validation\Factory as ValidatorFactory;
use Illuminate\Translation\Translator;
use Illuminate\Translation\FileLoader;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Validation\DatabasePresenceVerifier;

class Connection extends Capsule
{
    public $validator;

    public function __construct(array $connection, Container $container = null)
    {
        $container = new Container();
        parent::__construct($container);
        $this->addConnection($connection);
        $this->setAsGlobal();
        $this->bootEloquent();
    }

    public function setValidator(string $translation, string $langDirectory)
    {
        $filesystem = new FileLoader(new Filesystem(), $langDirectory);
        $translator = new Translator($filesystem, $translation);
        $validator = new ValidatorFactory($translator);
        $verifier = new DatabasePresenceVerifier($this->getDatabaseManager());
        $validator->setPresenceVerifier($verifier);
        $this->validator = $validator;
    }
}