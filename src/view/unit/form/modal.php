<?php

use models\Unit;
use controller\OauthController as Oauth;
use app\core\View;

/* @var $this View */
/* @var $unit Unit */
$rarities = Unit::getRarities();
$isLogged = Oauth::isLogged();
?>
<div class="form-group text-center">
    <button class="btn btn-default<?= (!$isLogged) ? ' disabled' : '' ?>" type="button" data-toggle="modal" data-target="#unit-update-modal-<?= $unit->id ?>">
        Edit
    </button>
</div>
<?php if ($isLogged): ?>
    <div class="modal fade" id="unit-update-modal-<?= $unit->id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <form id="<?= $unit->id ?>" class="ws-validate" method="post" role="form" action="<?=
            Main::$app->router->pathFor('unitUpdate', ['id' => $unit->id])
            ?>">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Update <?= $unit->original ?></h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Romaji name:</label>
                            <input class="form-control unit-name" name="name" type="text" value="<?=
                            ($unit->name) ? $unit->name : ''
                            ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Rarity:</label>
                            <select class="form-control unit-rarity" name="rarity">
                                <?php foreach ($rarities as $rarity): ?>
                                    <option value="<?= $rarity ?>" <?= ($rarity == $unit->rarity) ? 'selected' : '' ?>><?= $rarity ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input name="is_only_dmm" type="hidden" value="0">
                                <input name="is_only_dmm" value="1" type="checkbox"<?= ($unit->is_only_dmm) ? 'checked' : '' ?>> is only dmm?
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input name="is_male" type="hidden" value="0">
                                <input name="is_male" value="1" type="checkbox"<?= ($unit->is_male) ? 'checked' : '' ?>> is male?
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

