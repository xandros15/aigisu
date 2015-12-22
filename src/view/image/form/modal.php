<?php

use models\Image;
use models\Unit;
use app\core\View;

/* @var $this View */
/* @var $unit Unit */

$isImgReqired = $unit->isImagesRequired();
?>


<button class="btn btn-default<?= $isImgReqired ? '' : ' disabled' ?>" type="button" data-toggle="modal" data-target="#unit-image-upload-modal-<?= $unit->id ?>">
    Upload
</button>
<?php if ($isImgReqired): ?>
    <div class="modal fade" id="unit-image-upload-modal-<?= $unit->id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <form enctype="multipart/form-data" method="post" role="form" action=<?=
            Main::$app->router->pathFor('imageUpload', ['id' => $unit->id])
            ?>>
                <input type="hidden" name="MAX_FILE_SIZE" value="2097152" />
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Upload images</h4>
                    </div>
                    <div class="modal-body">
                        <?php
                        foreach (Image::getServersNames() as $server):
                            for ($scene = 1; $scene <= Image::IMAGE_PER_SERVER; $scene++):
                                if(!$unit->isImageExsists($server, $scene)):
                                    echo $this->render('image/form/modal-body',
                                        ['image' => (object) ['server' => $server, 'scene' => $scene]]);
                                endif;
                            endfor;
                        endforeach;
                        ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">upload images</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>