<?php

namespace app\core;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\DatabasePresenceVerifier;
use Illuminate\Validation\Factory as ValidatorFactory;
use Main;

class Connection
{
    /** @var ValidatorFactory */
    public $validator;

    /** @var Capsule */
    private $capsule;

    public function __construct(array $connection)
    {
        $this->setConnection($connection);
        $this->setValidator();
    }

    protected function setConnection(array $connection)
    {
        $this->capsule = new Capsule();
        $this->capsule->addConnection($connection);
        $this->capsule->setAsGlobal();
        $this->capsule->bootEloquent();
    }

    protected function setValidator()
    {
        $filesystem = new FileLoader(new Filesystem(), CONFIG_DIR . DIRECTORY_SEPARATOR . 'langs');
        $translator = new Translator($filesystem, Main::$app->web->get('locale', false) ? : 'en');

        $this->validator = new ValidatorFactory($translator);


        $verifier = new DatabasePresenceVerifier($this->capsule->getDatabaseManager());
        $this->validator->setPresenceVerifier($verifier);
    }
}