<?php

use Controllers\OauthController as Oauth;
use Models\Unit;
use Models\UnitSort;

/* @var $unitList \Illuminate\Database\Eloquent\Collection */
/* @var $pagination string */
/* @var $unitSort UnitSort */

$this->title = 'Units';
$this->containerClass = 'container';
$sort = $this->render('unit/sort', ['unitSort' => $unitSort]);
?>
<div id="units">
    <?php if (!$unitList->isEmpty()): ?>
        <div class="form-group">
            <?= $this->render('image/help') ?>
            <?php if (Oauth::isLogged()): ?>
                <button type="button" class="btn btn-primary ajax pull-right" data-target="<?=
                $this->pathFor('unitCreate') ?>">Create
                </button>
            <?php endif; ?>
        </div>
        <nav class="text-center">
            <?= $pagination ?>
        </nav>
        <ul class="unit-list list-group panel panel-default">
            <li class="panel-heading list-group-item">
                <?= $sort ?>
            </li>
            <?php foreach ($unitList as $unit): ?>
                <?php /** @var $unit Unit */ ?>
                <?= $this->render('unit/unit', ['unit' => $unit]) ?>
            <?php endforeach; ?>
        </ul>
        <nav class="text-center col-xs-12">
            <?= $pagination ?>
        </nav>
    <?php else: ?>
        <h3 class="text-center">Nothing found</h3>
    <?php endif; ?>
</div>