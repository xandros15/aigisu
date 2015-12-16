<?php

use RedBeanPHP\OODBBean;
use models\Units;
use app\core\View;

/* @var $this View */
/* @var $unit OODBBean */
$rarities = Units::getRarities();
?>
<div class="form-group text-center">
    <button class="btn btn-default" type="button" data-toggle="modal" data-target="#unit-update-modal-<?= $unit->id ?>">
        edit unit
    </button>
</div>
<div class="modal fade" id="unit-update-modal-<?= $unit->id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form id="<?= $unit->id ?>" method="post" role="form" action="<?=
        $this->getRouter()->pathFor('unitsUpdate', ['id' => $unit->id])
        ?>" style="margin-top: 5px">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Update <?= $unit->orginal ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Romaji name:</label>
                        <input class="form-control unit-name" name="unit[name]" type="text" value="<?=
                        ($unit->name) ? $unit->name : ''
                        ?>">
                    </div>
                    <div class="form-group">
                        <label>Rarity:</label>
                        <select class="form-control unit-rarity" name="unit[rarity]">
                            <?php foreach ($rarities as $rarity): ?>
                                <option value="<?= $rarity ?>" <?= ($rarity == $unit->rarity) ? 'selected' : '' ?>><?= $rarity ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input name="unit[isOnlyDmm]" type="checkbox"<?= ($unit->isOnlyDmm) ? 'checked' : '' ?>> is only dmm?
                        </label>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input name="unit[isMale]" type="checkbox"<?= ($unit->isMale) ? 'checked' : '' ?>> is male?
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" onclick="update(this)">update</button>
                </div>
            </div>
        </form>
    </div>
</div>

