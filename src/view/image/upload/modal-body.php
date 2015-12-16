<?php

use models\Image;
use RedBeanPHP\OODBBean;
use app\core\View;

/* @var $this View */
/* @var $image OODBBean */
$name = $image->server . $image->scene;
?>
<div class="row">
    <h4 class="text-center"><?= $image->server ?> <?= Image::imageSceneToHuman($image->scene) ?></h4>
    <div class="col-xs-12 form-group">
        <div class="col-xs-3">
            <label>File:</label>
        </div>
        <div class="col-xs-9">
            <input name="<?= $name ?>" type="file">
        </div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-3"><label>or</label></div>
    </div>
    <div class="col-xs-12 form-group">
        <div class="col-xs-3">
            <label>Source URL:</label>
        </div>
        <div class="col-xs-9">
            <input class="form-control" name="<?= $name ?>[url]" autocomplete="off" type="text"  placeholder="http://">
        </div>
    </div>
    <input type="hidden" name="<?= $name ?>[server]" value="<?= $image->server ?>">
    <input type="hidden" name="<?= $name ?>[scene]" value="<?= $image->scene ?>">
</div>