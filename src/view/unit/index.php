<?php

use controller\OauthController as Oauth;
use models\Unit;

/* @var $unitList \Illuminate\Database\Eloquent\Collection */
/* @var $pagination string */

$this->title = 'Units';
$this->containerClass = 'container';
?>
<div id="units">
    <?php if (!$unitList->isEmpty()): ?>
        <div class="col-xs-12 form-group">
            <?= $this->render('image/help') ?>
            <?php if (Oauth::isLogged()): ?>
                <button type="button" class="btn btn-primary ajax pull-right" data-target="<?=
                $this->pathFor('unitCreate') ?>">Create
                </button>
            <?php endif; ?>
        </div>
        <?= $pagination ?>
        <ul class="unit-list col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?= $this->render('unit/sort') ?>
                </div>
                <?php foreach ($unitList as $unit): ?>
                    <?php /** @var $unit Unit */ ?>
                    <?= $this->render('unit/unit', ['unit' => $unit]) ?>
                <?php endforeach; ?>
            </div>
        </ul>
        <?= $pagination ?>
    <?php else: ?>
        <h3 class="text-center">Nothing found</h3>
    <?php endif; ?>
</div>