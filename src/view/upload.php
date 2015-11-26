<?php $names = getImageColumsNames(); ?>
<div class="pull-left form-group text-center" style="width: 100px; margin: 0 15px 15px;">
    <?php if (!isDisabledUpload($unit) && !isCompletedUpload($unit)): ?>
        <button class="btn btn-default" type="button" data-toggle="modal" data-target="#unit-image-upload-modal-<?= $unit->id ?>">
            upload images
        </button>
    <?php endif; ?>
</div>
<div class="col-xs-8 col-sm-9">
    <div class="col-xs-12 col-sm-6 form-group">
        <?php foreach ($names as $title => $name): ?>
            <div class="form-group col-xs-6 text-center">
                <label><?= $title ?></label>
                <?php if (isDisabledUpload($unit, $name)): ?>
                    <span  class="glyphicon glyphicon-remove-sign" style="color:red;" aria-hidden="true"></span>
                <?php elseif (isCompletedUpload($unit, $name)): ?>
                    <span  class="glyphicon glyphicon-ok-sign" style="color:green;" aria-hidden="true"></span>
                <?php else: ?>
                    <span  class="glyphicon glyphicon-question-sign" style="color:blue;" aria-hidden="true"></span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php if (!isDisabledUpload($unit) && !isCompletedUpload($unit)): ?>
    <div class="modal fade" id="#unit-image-upload-modal-<?= $unit->id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                </div>
                <div class="modal-body">
                    <form enctype="multipart/form-data" method="post" role="form">
                        <?php foreach ($names as $title => $name): ?>
                            <?php if (!isDisabledUpload($unit) && !isCompletedUpload($unit)): ?>
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
                                        <input class="form-control" name="<?= $name ?>" type="text"  placeholder="http://">
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <div class="form-group col-xs-12" style="text-align: center">
                            <button class="btn btn-default" type="submit">upload images</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>