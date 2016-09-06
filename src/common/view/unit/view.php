<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-07-09
 * Time: 01:47
 */
use Aigisu\Common\Components\View\UrlExtension;
use Aigisu\Common\Components\View\View;

$tagsToString = function (array $tags) {
    $tags = array_map(function ($item) {
        if (is_object($item) && property_exists($item, 'name')) {
            $item = $item->name;
        } elseif (is_array($item)) {
            $item = isset($item['name']) ? $item['name'] : reset($item);
        }

        return str_replace('_', ' ', $item);
    }, $tags);

    return implode(', ', $tags);
};

/** @var $unit array */
/** @var $this View | UrlExtension */
$this->title = $unit['name'] . ' | Aigisu';
$unit['icon_name'] = $unit['icon_name'] ? $this->pathFor('unit.icon', [], ['name' => $unit['icon_name']]) : '';
$unit['tags'] = $tagsToString($unit['tags']);
$unit['images'] = $unit['images'] ? $this->pathFor('unit.images', ['id' => $unit['id']]) : '';
?>
<table class="table table-bordered">
    <?php foreach ($unit as $label => $item): ?>
        <tr>
            <th><?= $label ?></th>
            <td><?= $item ?></td>
        </tr>
    <?php endforeach; ?>
</table>