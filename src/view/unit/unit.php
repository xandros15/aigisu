<?php

use models\Unit;
use controller\OauthController as Oauth;

/* @var $unit Unit */
?>
<li id="unit-<?= $unit->id ?>" class="list-group-item media unit">
    <div class="buttons media-left ">
        <?php if (Oauth::isLogged()): ?>
            <div class="form-group text-center">
                <button type="button" class="btn ajax btn-default" data-target="<?=
                $this->pathFor('unitUpdate', ['id' => $unit->id])
                ?>">
                    Edit
                </button>
            </div>
        <?php endif; ?>
        <?php if ($unit->isImagesRequired()): ?>
            <div class="form-group text-center">
                <button type="button" class="btn ajax btn-default" data-target="<?=
                $this->pathFor('imageUpload', ['id' => $unit->id])
                ?>">
                    Upload
                </button>
            </div>
        <?php endif; ?>
    </div>
    <div class="media-left">
        <a target="_blank" href="<?= $unit->linkgc ?>">
            <img class="icon img-thumbnail<?= ($unit->isAnyImages()) ? ' success' : '' ?>" alt="" src="<?= $unit->icon ?>" data-bind="<?= $unit->id ?>">
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
        <input class="image-route" type="hidden" value="<?=
        $this->pathFor('image', ['id' => $unit->id])
        ?>">
    <?php endif; ?>
</li>