<?php

use models\Unit;
use app\core\View;

/* @var $this View */
/* @var $model Unit */

$this->setTitle('Units');
?>
<div id="units">
    <?php if (count($model) > 0): ?>
        <div class="col-xs-12"><?= $this->render('image/upload/help') ?></div>
        <?= $this->render('unit/pagination', ['maxPages' => $maxPages]) ?>
        <ul class="unit-list list-group  col-xs-12 col-xs-offset-0 col-sm-10  col-sm-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"><?= $this->render('unit/sort') ?></div>
                <?php foreach ($model as $unit): ?>
                    <li id="unit-<?= $unit->id ?>" class="list-group-item media unit">
                        <div class="buttons media-left">
                            <p class="text-right"><?= $this->render('unit/form/modal', ['unit' => $unit]); ?></p>
                            <p class="text-right"><button class="btn btn-default">Upload</button></p>
                        </div>
                        <div class="media-left">
                            <a target="_blank" href="<?= $unit->linkgc ?>">
                                <img class="icon" alt="" src="<?= $unit->icon ?>" data-bind="<?= $unit->id ?>">
                            </a>
                        </div>
                        <div class="media-body">
                            <div class="form-group">
                                <input class="form-control unit-name" value="<?= ($unit->name) ? $unit->name : '' ?>" readonly>
                            </div>
                            <div class="form-group">
                                <input class="form-control" type="text" value="<?= $unit->original ?>" readonly>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
        </ul>
    </div>
    <?= $this->render('unit/pagination', ['maxPages' => $maxPages]) ?>
<?php else: ?>
    <h3 class="text-center">Nothing found</h3>
<?php endif; ?>
</div>

