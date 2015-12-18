<?php

use controller\UnitController;
?>
<div class="row">
    <div class="col-xs-12">
        <label>Order by:</label>
        <label><a href="<?= UnitController::generateLink(['sort' => 'name']) ?> ">Name</a></label>
        <span style="margin: 0 3px;">|</span>
        <label><a href="<?= UnitController::generateLink(['sort' => 'original']) ?> ">Original name</a></label>
        <span style="margin: 0 3px;">|</span>
        <label><a href="<?= UnitController::generateLink(['sort' => 'rarity']) ?> ">Rarity</a></label>
    </div>
</div>