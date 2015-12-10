<?php

use RedBeanPHP\OODBBean;
use models\Oauth;

/* @var $unit OODBBean */
?>

<div id="unit-<?= $unit->id ?>" class="col-xs-12 col-sm-9 row">
    <div class="form-group pull-left">
        <a target="_blank" href="<?= $unit->linkgc ?>">
            <img class="icon" style="width: 100px" alt="" src="<?= $unit->icon ?>" data-bind="<?= $unit->id ?>">
        </a>
    </div>
    <div class="col-xs-8 col-sm-9">
        <div class="form-group col-xs-12">
            <input class="form-control unit-name" value="<?= ($unit->name) ? $unit->name : '' ?>" readonly>
        </div>
        <div class="form-group col-xs-12">
            <input class="form-control" type="text" value="<?= $unit->orginal ?>" readonly>
        </div>
    </div>
</div>
<?php if (Oauth::isLogged()): ?>
    <div class="col-xs-12 col-sm-2 row">
        <?= renderPhpFile('unit/form/modal', ['unit' => $unit]) ?>
    </div>
<?php endif; ?>