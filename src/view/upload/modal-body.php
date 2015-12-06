<?php

use models\Images;

/** @var $type */
$labels = Images::getImageLabels();
?>
<div class="row">
    <h4 class="text-center"><?= Images::imageNumberToHuman($labels[$type]) ?></h4>
    <div class="col-xs-12 form-group">
        <div class="col-xs-3">
            <label>File:</label>
        </div>
        <div class="col-xs-9">
            <input name="<?= $type ?>" type="file">
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
            <input class="form-control" name="<?= $type ?>" autocomplete="off" type="text"  placeholder="http://">
        </div>
    </div>
</div>