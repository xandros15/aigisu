<?php $names = ['DMM #1' => 'dmm1', 'DMM #2' => 'dmm2', 'Nutaku #1' => 'nutaku1', 'Nutaku #2' => 'nutaku2']; ?>
<?php foreach ($names as $title => $name): ?>
    <div class="form-group col-xs-12 col-sm-6" style="text-align: center">
        <label class="col-xs-12"><?= $title ?></label>
        <?php if (isDisabledUpload($unit, $name)): ?>
            <span  class="glyphicon glyphicon-ok" style="color:green;" aria-hidden="true"></span>
        <?php else: ?>
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
    </div>
<?php endforeach; ?>
<div class="form-group col-xs-12" style="text-align: center">
    <button class="btn btn-default" type="submit">upload images</button>
</div>