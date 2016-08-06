<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-06-18
 * Time: 17:00
 */

use Controllers\ImageController;
use Controllers\ImageFileController;
use Controllers\UnitController;
use Middlewares\ShowQueries;
use Middlewares\TrailingSlash;

/** @var $this \Aigisu\Main */
$this->map(['get'], '/', UnitController::class . ':actionIndex')->setName('home');
$this->group('/image', function () {
    /** @var $this \Aigisu\Main */
    $this->map(['post'], '/upload/{id:\d+}', ImageFileController::class . ':actionCreate')->setName('imageUpload');
    $this->map(['get'], '/{id:\d+}', ImageController::class . ':actionIndex')->setName('image');
});
$this->group('/unit', function () {
    /** @var $this \Aigisu\Main */
    $this->map(['get'], '[/]', UnitController::class . ':actionIndex')->setName('unit');
    $this->map(['get'], '/{id:\d+}', UnitController::class . ':actionView')->setName('unitView');
    $this->map(['post', 'get'], '/update/{id:\d+}', UnitController::class . ':actionUpdate')->setName('unitUpdate');
    $this->map(['post'], '/create', UnitController::class . ':actionCreate')->setName('unitCreate');
    $this->map(['get'], '/delete/{id:\d+}', UnitController::class . ':actionDelete')->setName('unitDelete');
});

$this->add(new TrailingSlash($this->getContainer()));

if ($this->getContainer()->get('settings')['displayErrorDetails']) {
    $this->add(new ShowQueries($this->getContainer()));
}