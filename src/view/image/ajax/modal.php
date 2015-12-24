<?php

use models\Image;
use models\Unit;
use app\core\View;

/* @var $this View */
/* @var $model Unit */
?>

<div class="modal fade" id="modal-upload-image-<?= $model->id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form enctype="multipart/form-data" class="ws-validate" method="post" role="form" action=<?=
        Main::$app->router->pathFor('imageUpload', ['id' => $model->id])
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
                            if (!$model->isImageExsists($server, $scene)):
                                echo $this->render('image/ajax/modal-body',
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