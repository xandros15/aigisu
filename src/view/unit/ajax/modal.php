<?php

use app\core\View;
use models\Unit;

/* @var $this View */
/* @var $model Unit */
$rarities  = Unit::getRarities();
$isNewUnit = (!$model->id);

$route = ($isNewUnit) ? Main::$app->router->pathFor('unitCreate') :
    Main::$app->router->pathFor('unitUpdate', ['id' => $model->id]);
?>
<div class="modal fade" id="modal-unit-<?= ($isNewUnit) ? '0' : $model->id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form method="post" role="form" class="ws-validate" action="<?= $route ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">
                        <?= ($isNewUnit) ? 'Create new unit' : 'Update ' . $model->name ?>
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Original name:</label>
                        <input class="form-control unit-original" name="original" type="text" value="<?= $model->original ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Romaji name:</label>
                        <input class="form-control unit-name" name="name" type="text" value="<?= $model->name ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Icon link:</label>
                        <input class="form-control unit-icon" name="icon" type="url" value="<?= $model->icon ?>" placeholder="http://" required>
                    </div>
                    <div class="form-group">
                        <label>Link to seesaw:</label>
                        <input class="form-control unit-link" name="link" type="url" value="<?= $model->link ?>" placeholder="http://">
                    </div>
                    <div class="form-group">
                        <label>Link to gc:</label>
                        <input class="form-control unit-linkgc" name="linkgc" type="url" value="<?= $model->linkgc ?>" placeholder="http://" required>
                    </div>
                    <div class="form-group">
                        <label>Rarity:</label>
                        <select class="form-control unit-rarity" name="rarity">
                            <?php foreach ($rarities as $rarity): ?>
                                <option value="<?= $rarity ?>" <?= ($rarity == $model->rarity) ? 'selected' : '' ?>><?= $rarity ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input name="is_only_dmm" type="hidden" value="0">
                            <input name="is_only_dmm" value="1" type="checkbox"<?= ($model->is_only_dmm) ? 'checked' : '' ?>> is only dmm?
                        </label>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input name="is_male" type="hidden" value="0">
                            <input name="is_male" value="1" type="checkbox"<?= ($model->is_male) ? 'checked' : '' ?>> is male?
                        </label>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input name="has_aw_image" type="hidden" value="0">
                            <input name="has_aw_image" value="1" type="checkbox"<?= ($model->has_aw_image) ? 'checked' : '' ?>> has aw image?
                        </label>
                    </div>
                    <div class="form-group">
                        <label>Tags:</label>
                        <textarea class="form-control" name="tags" rows="3"><?= $model->getTagsString() ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <?php if (!$isNewUnit): ?>
                        <a role="button" class="btn btn-danger pull-left" onclick="return confirm('Are you sure');" href="<?=
                        Main::$app->router->pathFor('unitDelete', ['id' => $model->id])
                        ?>">
                            delete
                        </a>
                    <?php endif; ?>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <?= ($isNewUnit) ? 'create' : 'update' ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>