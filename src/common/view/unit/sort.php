<?php

use Aigisu\Common\Models\UnitSort;

/** @var $unitSort UnitSort */

?>
<div class="row sort">
    <div class="col-xs-12">
        <label class="title">Order by:</label>
        <?php foreach ($unitSort->items() as $name) : ?>
            <label class="item item-<?= $name ?>"><a
                    href="<?= $unitSort->pathFor($name) ?> "><?= $unitSort->label($name) ?></a></label>
        <?php endforeach; ?>
    </div>
</div>