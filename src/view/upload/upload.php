<?php

use models\Images;

/* @var imagesSet models\Images */
/* @var $model Units */
$imagesSet = Images::imagesByUnit($unit->id, $model);
?>
<div class="pull-left form-group text-center" style="width: 100px; margin: 0 15px 15px;">
    <?php if (!$imagesSet->isCompletedUpload()): ?>
        <button class="btn btn-default" type="button" data-toggle="modal" data-target="#unit-image-upload-modal-<?= $unit->id ?>">
            upload images
        </button>
    <?php endif; ?>
</div>
<div class="col-xs-8 col-sm-9">
    <div class="col-xs-12 col-sm-7 form-group">
        <?php foreach ($imagesSet->getAllImages() as $image): ?>
            <div class="form-group col-xs-6 text-center">
                <label><?= "$image->server #$image->scene" ?></label>
                <?php if ($image->mode == Images::IMAGE_LOCKED): ?>
                    <span  class="glyphicon glyphicon-remove-sign" style="color:red;" aria-hidden="true"></span>
                <?php elseif ($image->mode == Images::IMAGE_AVAIABLE): ?>
                    <span  class="glyphicon glyphicon-ok-sign" style="color:green;" aria-hidden="true"></span>
                <?php elseif ($image->mode == Images::IMAGE_REQIRED): ?>
                    <span  class="glyphicon glyphicon-question-sign" style="color:blue;" aria-hidden="true"></span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php if (!$imagesSet->isCompletedUpload()): ?>
    <?= renderPhpFile('upload/modal', ['imagesSet' => $imagesSet]); ?>
<?php endif; ?>
<?php if ($imagesSet->isAnyImagesUploaded()): ?>
    <input class="is-any-images-uploaded" type="hidden" value="<?= $unit->id ?>">
<?php endif; ?>
