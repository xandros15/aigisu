<?php

use models\Unit;
use models\Oauth;
use app\core\View;

/* @var $this View */
/* @var $model Unit */
/* @var $units array */
$this->setTitle('Units');
?>
<div id="units" class="col-xs-12">
    <?php if (count($units) > 0): ?>
        <div class="col-xs-12"><?= $this->render('image/upload/help') ?></div>
        <?= $this->render('unit/pagination', ['model' => $model]) ?>
        <div class="unit-list col-xs-12 col-xs-offset-0 col-sm-10  col-sm-offset-2">
            <?= $this->render('unit/sort') ?>
            <?php foreach ($units as $unit): ?>
                <div class="single-unit row col-xs-12">
                    <div class="row">
                        <?= $this->render('unit/unit', ['unit' => $unit]) ?>
                    </div>
                    <?php if (Oauth::isLogged()): ?>
                        <div class="row">
                            <?= $this->render('unit/form/modal', ['unit' => $unit]) ?>
                        </div>
                    <?php endif; ?>
                    <div class="row">
                        <?= $this->render('image/upload/upload', ['model' => $model, 'unit' => $unit]); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?= $this->render('unit/pagination', ['model' => $model]) ?>
    <?php else: ?>
        <h3 class="text-center">Nothing found</h3>
    <?php endif; ?>
</div>

