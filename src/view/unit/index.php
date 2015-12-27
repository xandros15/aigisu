<?php

use models\Unit;
use app\core\View;
use controller\OauthController as Oauth;

/* @var $this View */
/* @var $model Unit */

$this->setTitle('Units');
?>
<div id="units">
    <?php if (count($model) > 0): ?>
        <div class="col-xs-12 form-group">
            <?= $this->render('image/help') ?>
            <?php if (Oauth::isLogged()): ?>
                <button type="button" class="btn btn-primary ajax pull-right" data-target="<?= 
                Main::$app->router->pathFor('unitCreate') ?>">Create</button>
            <?php endif; ?>
        </div>
        <?= $this->render('unit/pagination', ['maxPages' => $maxPages]) ?>
        <ul class="unit-list col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?= $this->render('unit/sort') ?>
                </div>
                <?php foreach ($model as $unit): ?>
                    <?= $this->render('unit/unit', ['unit' => $unit]) ?>
                <?php endforeach; ?>
            </div>
        </ul>
        <?= $this->render('unit/pagination', ['maxPages' => $maxPages]) ?>
    <?php else: ?>
        <h3 class="text-center">Nothing found</h3>
    <?php endif; ?>
</div>

