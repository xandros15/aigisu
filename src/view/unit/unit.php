<?php

use models\Unit;
use app\core\View;

/* @var $this View */
/* @var $unit Unit */
?>
<li id="unit-<?= $unit->id ?>" class="list-group-item media unit">
    <div class="buttons media-left">
        <p class="text-right"><?= $this->render('unit/form/modal', ['unit' => $unit]); ?></p>
        <p class="text-right"><?= $this->render('image/upload/form/modal', ['unit' => $unit]); ?></p>
    </div>
    <div class="media-left">
        <a target="_blank" href="<?= $unit->linkgc ?>">
            <img class="icon" alt="" src="<?= $unit->icon ?>" data-bind="<?= $unit->id ?>">
        </a>
    </div>
    <div class="media-body">
        <div class="form-group">
            <input class="form-control unit-name" value="<?= ($unit->name) ? $unit->name : '' ?>" readonly>
        </div>
        <div class="form-group">
            <input class="form-control" type="text" value="<?= $unit->original ?>" readonly>
        </div>
    </div>
</li>