<?php

use app\Images;
/* @var $images app\Images */
$types = Images::getTypeNames();
?>

<div class="modal fade" id="unit-image-upload-modal-<?= $images->unitId ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form enctype="multipart/form-data" method="post" role="form">
            <input type="hidden" name="id" value="<?= $images->unitId ?>">
            <input type="hidden" name="uploadImages" value="1">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Upload images</h4>
                </div>
                <div class="modal-body">
                    <?php foreach ($types as $type): ?>
                        <?php if (!$images->isDisabledUpload($type) && !$images->isCompletedUpload($type)): ?>
                            <?= renderPhpFile('upload/uploadModalBody', ['type' => $type]); ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">upload images</button>
                </div>
            </div>
        </form>
    </div>
</div>