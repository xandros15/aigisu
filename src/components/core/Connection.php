<?php

namespace app\core;

use Aigisu\Main;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Validation\Factory as ValidatorFactory;
use Illuminate\Translation\Translator;
use Illuminate\Translation\FileLoader;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Validation\DatabasePresenceVerifier;

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

    protected function setValidator()
    {
        $filesystem = new FileLoader(new Filesystem(), CONFIG_DIR . DIRECTORY_SEPARATOR . 'langs');
        $translator = new Translator($filesystem, Main::$app->web->get('locale', false) ? : 'en');

        $this->validator = new ValidatorFactory($translator);


        $verifier = new DatabasePresenceVerifier($this->capsule->getDatabaseManager());
        $this->validator->setPresenceVerifier($verifier);
    }

    protected function setConnection(array $connection)
    {
        $this->capsule = new Capsule();
        $this->capsule->addConnection($connection);
        $this->capsule->setAsGlobal();
        $this->capsule->bootEloquent();
    }
}