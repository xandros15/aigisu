<?php

use Illuminate\Database\Eloquent\Collection;
use Models\Unit;
use Models\UnitSort;
use Xandros15\SlimPagination\Pagination;

/* @var $unitList Collection */
/* @var $pagination Pagination */
/* @var $unitSort UnitSort */

$this->title = 'Units';
$this->containerClass = 'container';
/** @var $unitSort string */
$unitSort = $this->render('unit/sort', ['unitSort' => $unitSort]);
/** @var $pagination string */
$pagination = $this->render('unit/pagination', ['pagination' => $pagination]);
?>
<div id="units">
    <?php if (!$unitList->isEmpty()): ?>
        <div class="form-group">
            <?= $this->render('image/help') ?>
            <button type="button" class="btn btn-primary ajax pull-right" data-target="<?=
            $this->pathFor('unitCreate') ?>">Create
            </button>
        </div>
        <nav class="text-center">
            <?= $pagination ?>
        </nav>
        <ul class="unit-list list-group panel panel-default">
            <li class="panel-heading list-group-item">
                <?= $unitSort ?>
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