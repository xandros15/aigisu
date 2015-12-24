<?php

use models\Unit;
use app\core\View;
use controller\OauthController as Oauth;

/* @var $this View */
/* @var $unit Unit */
?>
<li id="unit-<?= $unit->id ?>" class="list-group-item media unit">
    <div class="buttons media-left">
        <div class="form-group text-center">
            <button class="btn ajax btn-default<?=
                    (!Oauth::isLogged()) ? ' disabled' : ''
                    ?>" type="button" data-target="<?=
                    Main::$app->router->pathFor('unitUpdate', ['id' => $unit->id])
                    ?>">
                Edit
            </button>
        </div>
    <p class="text-right"><?= $this->render('image/form/modal', ['unit' => $unit]); ?></p>
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
<?php if ($unit->isAnyImages()): ?>
    <input class="is-any-images-uploaded" type="hidden" value="<?= $unit->id ?>">
    <input class="image-route" type="hidden" value="<?= Main::$app->router->pathFor('image', ['id' => $unit->id]) ?>">
<?php endif; ?>
</li>