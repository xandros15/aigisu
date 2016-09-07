<?php

use Aigisu\Common\Components\View\UrlExtension;
use Aigisu\Common\Components\View\View;
use Aigisu\Common\Models\UnitSort;
use Illuminate\Database\Eloquent\Collection;
use Xandros15\SlimPagination\Pagination;

/* @var $units Collection */
/* @var $pagination Pagination */
/* @var $unitSort UnitSort */
/* @var $this UrlExtension | View */

$this->title = 'Units';
/** @var $unitSort string */
$unitSort = $this->render('unit/sort', ['unitSort' => $unitSort]);
/** @var $pagination string */
$pagination = $this->render('unit/pagination', ['pagination' => $pagination]);

$units = $units->map(function ($unit) {
    $applyColumn = function (&$column, $key) {
        $element = $key === 'id' ? 'th' : 'td';
        $column = "<$element>{$column}</$element>";
    };
    array_walk($unit, function (&$param) {
        if (is_scalar($param)) {
            $param = htmlentities($param);
        }
    });

    /* @var $this UrlExtension */
    $iconSrc = $this->pathFor('unit.icon', [], ['name' => $unit['icon']]);
    $unitPath = $this->pathFor('unit.view', ['id' => $unit['id']]);
    $params = [
        '#' => $unit['id'],
        'Unit' => "<a href='{$unitPath}' >{$unit['name']}</a>",
        'Icon' => "<img style='max-width:98px;' class='img-responsive' src='{$iconSrc}' alt='{$unit['name']}'>",
        'Kanji' => $unit['kanji'],
        'Links' => "<a class='btn btn-default' style='margin:5px;' href='{$unit['links']['seesaw']}'>seesaw</a>" .
            "<a class='btn btn-default' style='margin:5px;' href='{$unit['links']['gc']}'>gc wiki</a>",
        'Rarity' => $unit['rarity'],
        'Created' => "<time>{$unit['created_at']}</time>",
        'Updated' => "<time>{$unit['updated_at']}</time>",
    ];

    array_walk($params, $applyColumn);
    return $params;
});

$labels = array_keys($units->first());
array_walk($labels, function (&$label) {
    $label = "<th>{$label}</th>";
})

?>
<div id="units">
    <div class="form-group">
        <?= $this->render('image/help') ?>
    </div>
    <?php if (!$units->isEmpty()): ?>
        <nav class="text-center">
            <?= $pagination ?>
        </nav>
        <?= $unitSort ?>
        <table class="table table-bordered">
            <thead>
            <tr><?= implode("\r\n\t", $labels) ?></tr>
            </thead>
            <tbody>
            <?php foreach ($units as $unit): ?>
                <tr><?= implode("\r\n\t", $unit) ?></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <nav class="text-center col-xs-12">
            <?= $pagination ?>
        </nav>
    <?php else: ?>
        <h3 class="text-center">Nothing found</h3>
    <?php endif; ?>
</div>