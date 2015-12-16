<?php

use models\Units;
use models\Oauth;

/* @var $model Units */
/* @var $units array */
?>
<div id="units" class="col-xs-12">
    <?php if (count($units) > 0): ?>
        <div class="col-xs-12"><?= renderPhpFile('upload/help') ?></div>
        <?= renderPhpFile('unit/pagination', ['model' => $model]) ?>
        <div class="unit-list col-xs-12 col-xs-offset-0 col-sm-10  col-sm-offset-2">
            <?= renderPhpFile('unit/sort') ?>
            <?php foreach ($units as $unit): ?>
                <div class="single-unit row col-xs-12">
                    <div class="row">
                        <?= renderPhpFile('unit/unit', ['unit' => $unit]) ?>
                    </div>
                    <?php if (Oauth::isLogged()): ?>
                        <div class="row">
                            <?= renderPhpFile('unit/form/modal', ['unit' => $unit]) ?>
                        </div>
                    <?php endif; ?>
                    <div class="row">
                        <?= renderPhpFile('upload/upload', ['model' => $model, 'unit' => $unit]); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?= renderPhpFile('unit/pagination', ['model' => $model]) ?>
    <?php else: ?>
        <h3 class="text-center">Nothing found</h3>
    <?php endif; ?>
</div>

