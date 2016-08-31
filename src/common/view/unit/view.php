<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-07-09
 * Time: 01:47
 */
use Aigisu\Api\Models\Unit;
use Aigisu\Common\Components\View\UrlExtension;
use Aigisu\Common\Components\View\View;
use Slim\Collection;

/** @var $unit Collection */
/** @var $this View | UrlExtension */
$rarities = Unit::getRarities();
$isNewUnit = empty($unit['id']);

if (!$isNewUnit) {
    $this->title = $unit['name'] . ' | Aigisu';
    $pathForCreateOrUpdate = $this->pathFor('unit.update', ['id' => $unit['id']]);
    $pathForDelete = $this->pathFor('unit.delete', ['id' => $unit['id']]);
    $pathForIcon = $unit['icon_name'] ? $this->pathFor('unit.icon', ['name' => $unit['icon_name']]) : '';
    $unit['tags'] = Unit::arrayToTags($unit['tags']);
} else {
    $this->title = 'New Unit | Aigisu';
    $pathForCreateOrUpdate = $this->pathFor('unit.create');
    $unit['id'] = 'create';
}

?>

<form method="post" enctype="multipart/form-data" role="form" data-toggle="validator"
      action="<?= $pathForCreateOrUpdate ?>">
    <header class="form-header row">
        <div class="col-xs-12 col-sm-6">
            <h2 class="form-title">
                <?= ($isNewUnit) ? 'Create new unit' : 'Update ' . $unit['name'] ?>
            </h2>
        </div>
        <?php if (!empty($pathForIcon)) : ?>
            <div class="col-xs-12 col-sm-6">
                <img src="<?= $pathForIcon ?>" class="pull-right img-circle" alt="...">
            </div>
        <?php endif; ?>
    </header>
    <section class="form-body row">
        <div class="form-group col-xs-12 col-sm-6">
            <label for="unit-original-<?= $unit['id'] ?>">Original name:</label>
            <input id="unit-original-<?= $unit['id'] ?>" class="form-control unit-original" name="original"
                   type="text"
                   value="<?= $unit['original'] ?>" required>
        </div>
        <div class="form-group col-xs-12 col-sm-6">
            <label for="unit-name-<?= $unit['id'] ?>">Romaji name:</label>
            <input id="unit-name-<?= $unit['id'] ?>" class="form-control unit-name" name="name" type="text"
                   value="<?= $unit['name'] ?>"
                   required>
        </div>
        <div class="form-group col-xs-12 col-sm-6">
            <label for="unit-icon-<?= $unit['id'] ?>">Icon:</label>
            <input id="unit-icon-<?= $unit['id'] ?>" class="form-control unit-icon" name="icon" type="file">
        </div>
        <div class="form-group col-xs-12 col-sm-6">
            <label for="unit-link-<?= $unit['id'] ?>">Link to seesaw:</label>
            <input id="unit-link-<?= $unit['id'] ?>" class="form-control unit-link" name="link" type="url"
                   value="<?= $unit['link'] ?>"
                   placeholder="http://">
        </div>
        <div class="form-group col-xs-12 col-sm-6">
            <label for="unit-linkgc-<?= $unit['id'] ?>">Link to gc:</label>
            <input id="unit-linkgc-<?= $unit['id'] ?>" class="form-control unit-linkgc" name="linkgc"
                   type="url" value="<?= $unit['linkgc'] ?>"
                   placeholder="http://" required>
        </div>
        <div class="form-group col-xs-12 col-sm-6">
            <label for="unit-rarity-<?= $unit['id'] ?>">Rarity:</label>
            <select id="unit-rarity-<?= $unit['id'] ?>" class="form-control unit-rarity" name="rarity">
                <?php foreach ($rarities as $rarity): ?>
                    <option
                        value="<?= $rarity ?>" <?= ($rarity == $unit['rarity']) ? 'selected' : '' ?>><?= $rarity ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="checkbox col-xs-12 col-sm-6">
            <label>
                <input name="is_only_dmm" type="hidden" value="0">
                <input name="is_only_dmm" value="1"
                       type="checkbox"<?= ($unit['is_only_dmm']) ? 'checked' : '' ?>> is only dmm?
            </label>
        </div>
        <div class="checkbox col-xs-12 col-sm-6">
            <label>
                <input name="is_male" type="hidden" value="0">
                <input name="is_male" value="1" type="checkbox"<?= ($unit['is_male']) ? 'checked' : '' ?>> is
                male?
            </label>
        </div>
        <div class="checkbox col-xs-12 col-sm-6">
            <label>
                <input name="has_aw_image" type="hidden" value="0">
                <input name="has_aw_image" value="1"
                       type="checkbox"<?= ($unit['has_aw_image']) ? 'checked' : '' ?>> has aw image?
            </label>
        </div>
        <div class="form-group col-xs-12 col-sm-6">
            <label for="unit-tags-<?= $unit['id'] ?>">Tags:</label>
                        <textarea id="unit-tags-<?= $unit['id'] ?>" class="form-control unit-tags" name="tags"
                                  rows="3"><?= $unit['tags'] ?></textarea>
        </div>
    </section>
    <footer class="form-footer row">
        <div class="col-xs-3">
            <button type="submit" class="btn btn-block btn-primary">
                <?= ($isNewUnit) ? 'create' : 'update' ?>
            </button>
        </div>
        <?php if (isset($pathForDelete)): ?>
            <div class="col-xs-3 pull-right">
                <a role="button" class="btn btn-block btn-danger pull-left" onclick="return confirm('Are you sure');"
                   href="<?= $pathForDelete ?>">
                    delete
                </a>
            </div>
        <?php endif; ?>
    </footer>
</form>