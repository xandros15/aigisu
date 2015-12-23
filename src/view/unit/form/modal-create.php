<?php

use models\Unit;
use controller\OauthController as Oauth;
use app\core\View;

/* @var $this View */
/* @var $unit Unit */
$rarities = Unit::getRarities();
$isLogged = Oauth::isLogged();
?>
<?php if ($isLogged): ?>
    <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#modal-unit-create">
        Create
    </button>
    <div class="modal fade" id="modal-unit-create" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <form method="post" role="form" class="ws-validate" action="<?=
            Main::$app->router->pathFor('unitCreate')
            ?>">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Create new unit</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Original name:</label>
                            <input class="form-control unit-name" name="original" type="text" required>
                        </div>
                        <div class="form-group">
                            <label>Romaji name:</label>
                            <input class="form-control unit-name" name="name" type="text" required>
                        </div>
                        <div class="form-group">
                            <label>Icon link:</label>
                            <input class="form-control unit-name" name="icon" type="url" placeholder="http://" required>
                        </div>
                        <div class="form-group">
                            <label>Link to seesaw:</label>
                            <input class="form-control unit-name" name="link" type="url" placeholder="http://">
                        </div>
                        <div class="form-group">
                            <label>Link to gc:</label>
                            <input class="form-control unit-name" name="linkgc" type="url" placeholder="http://" required>
                        </div>
                        <div class="form-group">
                            <label>Rarity:</label>
                            <select class="form-control unit-rarity" name="rarity">
                                <?php foreach ($rarities as $rarity): ?>
                                    <option value="<?= $rarity ?>"><?= $rarity ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input name="is_only_dmm" type="hidden" value="0">
                                <input name="is_only_dmm" value="1" type="checkbox" checked> is only dmm?
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input name="is_male" type="hidden" value="0">
                                <input name="is_male" value="1" type="checkbox" checked> is male?
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>