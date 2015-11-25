<div id="units">
    <?php $units = findUnits(); ?>
    <?php if (count($units) > 0): ?>
        <?= renderPhpFile('pagination') ?>
        <?= renderPhpFile('sort') ?>
        <?php $raritis = enumRarity() ?>
        <?php foreach ($units as $unit): ?>
            <form id="<?= $unit->id ?>" method="post" role="form" style="margin-top: 5px" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $unit->id ?>">
                <div class="row">
                    <div class="form-group col-xs-3 col-sm-2">
                        <a target="_blank" href="<?= $unit->linkgc ?>">
                            <img style="height: 100px" alt="" src="<?= $unit->icon ?>" data-bind="<?= $unit->id ?>">
                        </a>
                    </div>
                    <div class="col-xs-9 col-sm-10">
                        <div class="form-group col-xs-9 col-sm-4">
                            <input class="form-control unit-name" name="unit[name]" type="text" <?=
                            ($unit->name) ? 'value="' . $unit->name . '"' : ''
                            ?>>
                        </div>

                        <div class="form-group col-xs-9 col-sm-4">
                            <input class="form-control" type="text" value="<?= $unit->orginal ?>" readonly>
                        </div>
                        <div class="form-group col-xs-9 col-sm-4">
                            <select class="form-control unit-rarity" name="unit[rarity]">
                                <?php foreach ($raritis as $rarity): ?><?php
                                    $selected = ($rarity == $unit->rarity) ? 'selected' : ''
                                    ?>
                                    <option value="<?= $rarity ?>" <?= $selected ?>><?= $rarity ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-9 col-sm-10">
                        <div class="col-xs-9">
                            <div class="form-group col-xs-6" style="text-align: center">
                                <span  class="btn-file btn btn-default<?= isDisabledUpload($unit, 'dmm1') ? ' disabled'
                                    : '' ?>" type="button">DMM #1<input name="dmm1" type="file"></span>
                            </div>
                            <div class="form-group col-xs-6" style="text-align: center">
                                <span class="btn-file btn btn-default<?= isDisabledUpload($unit, 'nutaku1') ? ' disabled'
                                    : '' ?>" type="button">Nutaku #1<input name="nutaku1" type="file"></span>
                            </div>
                            <div class="form-group col-xs-6" style="text-align: center">
                                <span  class="btn-file btn btn-default<?= isDisabledUpload($unit, 'dmm2') ? ' disabled'
                                    : '' ?>" type="button">DMM #2<input name="dmm2" type="file"></span>
                            </div>
                            <div class="form-group col-xs-6" style="text-align: center">
                                <span  class="btn-file btn btn-default<?= isDisabledUpload($unit, 'nutaku2') ? ' disabled'
                                    : '' ?>" type="button">Nutaku #2<input name="nutaku2" type="file"></span>
                            </div>
                        </div>
                        <div class="col-xs-3">
                            <div class="form-group col-xs-12" style="text-align: center">
                                <button class="btn btn-default" type="button" onclick="update(this)">update</button>
                            </div>
                            <div class="form-group col-xs-12" style="text-align: center">
                                <button class="btn btn-default" type="submit">upload images</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        <?php endforeach; ?>
        <?= renderPhpFile('pagination') ?>
    <?php else: ?>
        <h3 class="text-center">Nothing found</h3>
    <?php endif; ?>
</div>

