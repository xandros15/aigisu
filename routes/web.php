<?php
//silence is golden

/** @var $this \Slim\App */

use Aigisu\Web\Controllers\UnitController;

$this->get('[/]', function () {
    list(, $response) = func_get_args();
    /** @var $response \Slim\Http\Response */
    return $response->withRedirect('/units');
});

$this->get('/units', UnitController::class . ':actionIndex')->setName('web.units');
