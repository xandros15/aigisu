<?php

use Aigisu\Api\Models\Unit;

/* @var $unit Unit */
$pathForView = $this->pathFor('unit.view', ['id' => $unit->id]);
$pathForUpload = $this->pathFor('image.create', ['id' => $unit->id]);
?>
<li id="unit-<?= $unit->id ?>" class="list-group-item media unit">
    <div class="buttons media-left ">
        <div class="form-group text-center">
            <a type="button" class="btn btn-default" data-target="<?= $pathForView ?>"
               href="<?= $pathForView ?>">
                Edit
            </a>
        </div>
    </div>
    <div class="media-left">
        <a target="_blank" href="<?= $unit->linkgc ?>">
            <img class="icon img-thumbnail" alt=""
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
        <div class="tags">
            <?php foreach ($unit->tags as $tag): ?>
                <span class="label label-default"><?= $tag['name'] ?></span>
            <?php endforeach; ?>
        </div>
    </div>
</li>