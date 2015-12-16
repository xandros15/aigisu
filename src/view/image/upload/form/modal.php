<?php

use models\Image;
use app\core\View;

/* @var $this View */
/* @var $imagesSet Image */
$images = $imagesSet->getAllImages(Image::IMAGE_REQIRED);
?>

<div class="modal fade" id="unit-image-upload-modal-<?= $imagesSet->unitId ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form enctype="multipart/form-data" method="post" role="form" action=<?=
        Main::$app->router->pathFor('imageUpload', ['id' => $imagesSet->unitId])
        ?>>
            <input type="hidden" name="MAX_FILE_SIZE" value="2097152" />
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Upload images</h4>
                </div>
                <div class="modal-body">
                    <?php foreach ($images as $image): ?>
                        <?= $this->render('image/upload/form/modal-body', ['image' => $image]); ?>
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