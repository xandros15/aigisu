<?php

namespace Aigisu;

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\DatabasePresenceVerifier;
use Illuminate\Validation\Factory as ValidatorFactory;

class Connection extends Capsule
{

    public function __construct(array $connection)
    {
        $container = new Container();
        Container::setInstance($container);
        parent::__construct($container);
        $this->addConnection($connection);
    }

    public function setValidator(string $translation, string $langDirectory)
    {
        $filesystem = new FileLoader(new Filesystem(), $langDirectory);
        $translator = new Translator($filesystem, $translation);
        $validator = new ValidatorFactory($translator);
        $verifier = new DatabasePresenceVerifier($this->getDatabaseManager());
        $validator->setPresenceVerifier($verifier);
        Container::getInstance()->offsetSet('validator', $validator);
    }
}