<?php

use models\Unit;
use app\core\View;

/* @var $this View */
/* @var $model Unit */

$this->setTitle('Units');
?>
<div id="units">
    <?php if (count($model) > 0): ?>
    <div class="col-xs-12"><?= $this->render('image/help') ?><?= $this->render('unit/form/modal-create') ?></div>
        <?= $this->render('unit/pagination', ['maxPages' => $maxPages]) ?>
        <ul class="unit-list list-group col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?= $this->render('unit/sort') ?>
                </div>
                <?php foreach ($model as $unit): ?>
                    <?= $this->render('unit/unit', ['unit' => $unit]) ?>
                <?php endforeach; ?>
            </div>
        </ul>
        <?= $this->render('unit/pagination', ['maxPages' => $maxPages]) ?>
    <?php else: ?>
        <h3 class="text-center">Nothing found</h3>
    <?php endif; ?>
</div>

