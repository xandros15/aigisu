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

/** @var $unit array */
/** @var $this View | UrlExtension */
$this->title = $unit['name'] . ' | Aigisu';
$unit['icon_name'] = $unit['icon_name'] ? $this->pathFor('unit.icon', ['name' => $unit['icon_name']]) : '';
$unit['tags'] = Unit::arrayToTags($unit['tags']);
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