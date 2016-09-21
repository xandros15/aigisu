<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-07-09
 * Time: 01:47
 */
use Aigisu\Common\Components\View\UrlExtension;

function prepareUnit($unit, $router)
{
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

    $unit['tags'] = $tagsToString($unit['tags']);
    $unit['links'] = array_map(function ($item) {
        return "<a href=\"{$item}\">{$item}</a>";
    }, $unit['links']);

    if (!empty($unit['cg'])) {
        /** @var $router UrlExtension */
        $CGPath = $router->pathFor('unit.cg', ['id' => $unit['id']]);
        $unit['cg'] = "<a href=\"{$CGPath}\">{$CGPath}</a>";
    }
    $unit['icon'] = "<a href=\"{$unit['icon']}\">{$unit['icon']}</a>";

    foreach ($unit as &$param) {
        if (is_bool($param)) {
            $param = $param ? 'Yes' : 'No';
        }
    }

    return $unit;
}

/** @var $unit array */
$unit = prepareUnit($unit, $this);

$this->title = $unit['name'] . ' | Aigisu';

?>
<table class="table table-bordered">
    <?php foreach ($unit as $label => $item): ?>
        <tr>
            <th><?= $label ?></th>
            <td><?= is_array($item) ? implode('<br>', $item) : $item ?></td>
        </tr>
    <?php endforeach; ?>
</table>