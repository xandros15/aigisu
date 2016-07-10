<?php

use Controllers\OauthController as Oauth;
use Models\Unit;

/* @var $unit Unit */
$pathForView = $this->pathFor('unitView', ['id' => $unit->id]);
?>
<li id="unit-<?= $unit->id ?>" class="list-group-item media unit">
    <div class="buttons media-left ">
        <?php if (Oauth::isLogged()): ?>
            <div class="form-group text-center">
                <a type="button" class="btn btn-default" data-target="<?= $pathForView ?>"
                   href="<?= $pathForView ?>">>
                    Edit
                </a>
            </div>
        <?php endif; ?>
        <?php if ($unit->isImagesRequired()): ?>
            <div class="form-group text-center">
                <button type="button" class="btn btn-default" data-target="<?=
                $this->pathFor('imageUpload', ['id' => $unit->id])
                ?>">
                    Upload
                </button>
            </div>
        <?php endif; ?>
    </div>
    <div class="media-left">
        <a target="_blank" href="<?= $unit->linkgc ?>">
            <img class="icon img-thumbnail<?= ($unit->isAnyImages()) ? ' success' : '' ?>" alt=""
                 src="<?= $unit->icon ?>" data-bind="<?= $unit->id ?>">
        </a>
    </div>
    <div class="media-body">
        <div class="form-group">
            <input title="unit-name" class="form-control unit-name" value="<?= ($unit->name) ? $unit->name : '' ?>"
                   readonly>
        </div>
        <div class="form-group">
            <input title="unit-original" class="form-control unit-original" type="text" value="<?= $unit->original ?>"
                   readonly>
        </div>
    </div>
    <?php if ($unit->isAnyImages()): ?>
        <input class="is-any-images-uploaded" type="hidden" value="<?= $unit->id ?>">
        <input class="image-route" type="hidden" value="<?=
        $this->pathFor('image', ['id' => $unit->id])
        ?>">
    <?php endif; ?>
</li>