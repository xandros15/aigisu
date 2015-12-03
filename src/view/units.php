<div id="units" class="col-xs-12">
    <?php $units = findUnits(); ?>
    <?php if (count($units) > 0): ?>
        <div class="col-xs-12"><?= renderPhpFile('upload/uploadHelp') ?></div>
        <?= renderPhpFile('pagination') ?>
        <?= renderPhpFile('sort') ?>
        <?php $rarities = getEnumRarity() ?>
        <?php foreach ($units as $unit): ?>
            <div class="row col-xs-12">
                <form id="<?= $unit->id ?>" method="post" role="form" style="margin-top: 5px">
                    <input type="hidden" name="id" value="<?= $unit->id ?>">
                    <div class="form-group pull-left">
                        <a target="_blank" href="<?= $unit->linkgc ?>">
                            <img class="icon" style="width: 100px" alt="" src="<?= $unit->icon ?>" data-bind="<?= $unit->id ?>">
                        </a>
                    </div>
                    <div class="col-xs-9 col-sm-10">
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group col-xs-12 col-sm-12">
                                <input class="form-control unit-name" name="unit[name]" type="text" <?=
                                ($unit->name) ? 'value="' . $unit->name . '"' : ''
                                ?>>
                            </div>
                            <div class="form-group col-xs-12 col-sm-12">
                                <input class="form-control" type="text" value="<?= $unit->orginal ?>" readonly>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group col-xs-12">
                                <select class="form-control unit-rarity" name="unit[rarity]">
                                    <?php foreach ($rarities as $rarity): ?><?php
                                        $selected = ($rarity == $unit->rarity) ? 'selected' : ''
                                        ?>
                                        <option value="<?= $rarity ?>" <?= $selected ?>><?= $rarity ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-xs-12 text-center">
                                <button class="btn btn-default" type="button" onclick="update(this)">update</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="row col-xs-12">
                <?= renderPhpFile('upload/upload', ['unit' => $unit]); ?>
            </div>
        <?php endforeach; ?>
        <?= renderPhpFile('pagination') ?>
    <?php else: ?>
        <h3 class="text-center">Nothing found</h3>
    <?php endif; ?>
</div>

