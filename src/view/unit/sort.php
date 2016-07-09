<?php

use Models\UnitSort;

/** @var $unitSort UnitSort */
?>
<div class="row sort">
    <div class="col-xs-12">
        <label class="title">Order by:</label>
        <?php foreach ($unitSort->items() as $name => $label) : ?>
            <label class="item item-<?= $name ?>"><a href="<?= $unitSort->pathFor($name) ?> "><?= $label ?></a></label>
        <?php endforeach; ?>
    </div>
</div>