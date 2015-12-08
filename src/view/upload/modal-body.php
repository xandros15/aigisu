<?php

use models\Images;
use RedBeanPHP\OODBBean;

/* @var $image OODBBean */
?>
<div class="row">
    <h4 class="text-center"><?= $image->server ?> <?= Images::imageSceneToHuman($image->scene) ?></h4>
    <div class="col-xs-12 form-group">
        <div class="col-xs-3">
            <label>File:</label>
        </div>
        <div class="col-xs-9">
            <input name="<?= $image->server . $image->scene ?>" type="file">
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
            <input class="form-control" name="<?= $image->server . $image->scene ?>" autocomplete="off" type="text"  placeholder="http://">
        </div>
    </div>
</div>