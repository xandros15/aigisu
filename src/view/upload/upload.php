<?php

use app\Images;

$images = Images::load($unit);
$types  = Images::getTypeNames();
$labels = Images::getImageLabels();
?>
<div class="pull-left form-group text-center" style="width: 100px; margin: 0 15px 15px;">
    <?php if (!$images->isDisabledUpload() && !$images->isCompletedUpload()): ?>
        <button class="btn btn-default" type="button" data-toggle="modal" data-target="#unit-image-upload-modal-<?= $unit->id ?>">
            upload images
        </button>
    <?php endif; ?>
</div>
<div class="col-xs-8 col-sm-9">
    <div class="col-xs-12 col-sm-7 form-group">
        <?php foreach ($types as $type): ?>
            <div class="form-group col-xs-6 text-center">
                <label><?= $labels[$type] ?></label>
                <?php if ($images->isDisabledUpload($type)): ?>
                    <span  class="glyphicon glyphicon-remove-sign" style="color:red;" aria-hidden="true"></span>
                <?php elseif ($images->isCompletedUpload($type)): ?>
                    <span  class="glyphicon glyphicon-ok-sign" style="color:green;" aria-hidden="true"></span>
                <?php else: ?>
                    <span  class="glyphicon glyphicon-question-sign" style="color:blue;" aria-hidden="true"></span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php if (!$images->isDisabledUpload() && !$images->isCompletedUpload()): ?>
    <?= renderPhpFile('upload/uploadModal', ['images' => $images]); ?>
<?php endif; ?>
<?php if ($images->isAnyImagesUploaded()): ?>
    <input class="is-any-images-uploaded" type="hidden" value="<?= $images->unitId ?>">
<?php endif; ?>
