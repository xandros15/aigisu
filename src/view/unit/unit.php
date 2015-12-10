<?php

use RedBeanPHP\OODBBean;

/* @var $unit OODBBean */
?>

<div id="unit-<?= $unit->id ?>" class="col-xs-12 row">
    <div class="pull-left">
        <a target="_blank" href="<?= $unit->linkgc ?>">
            <img class="icon" style="width: 100px" alt="" src="<?= $unit->icon ?>" data-bind="<?= $unit->id ?>">
        </a>
    </div>
    <div class="col-xs-7 col-sm-8">
        <div class="form-group col-xs-12">
            <input class="form-control unit-name" value="<?= ($unit->name) ? $unit->name : '' ?>" readonly>
        </div>
        <div class="form-group col-xs-12">
            <input class="form-control" type="text" value="<?= $unit->orginal ?>" readonly>
        </div>
    </div>
</div>