<div id="classes" class="row">
    <div class="input col-xs-12">
        <form method="post" role="form">
            <div class="col-xs-12 col-sm-4 form-group">
                <label class="control-label">First class</label>
                <input class="form-control" name="class[first]" type="text">
            </div>
            <div class="col-xs-12 col-sm-4 form-group">
                <label class="control-label">Second class</label>
                <input class="form-control" name="class[second]" type="text">
            </div>
            <div class="col-xs-12 col-sm-4 form-group">
                <label class="control-label">Third class</label>
                <input class="form-control" name="class[third]" type="text">
            </div>
            <div class="col-xs-12 form-group">
                <label class="control-label">&nbsp;</label>
                <div class="" style="text-align: center">
                    <button class="btn btn-default" type="submit">Create</button>
                </div>
            </div>
        </form>
    </div>
    <div class="list col-sm-12 col-xs-12" style="margin-top: 10px">
        <?php $classes = R::findAll('class'); ?>
        <?php if ($classes): ?>
            <form class="class-list">
                <ul><?php foreach ($classes as $class): ?><li class="col-sm-6 col-xs-12"><label class="checkbox"><input type="checkbox" value="<?= $class->id ?>" checked><?= $class->name ?></label></li><?php endforeach; ?></ul>
            </form>
        <?php endif; ?>
    </div>
</div>
