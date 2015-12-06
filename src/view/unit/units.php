<?php
$units = findUnits();
?>
<div id="units" class="col-xs-12">
    <?php if (count($units) > 0): ?>
        <div class="col-xs-12"><?= renderPhpFile('upload/help') ?></div>
        <?= renderPhpFile('unit/pagination') ?>
        <?= renderPhpFile('unit/sort') ?>
        <?php foreach ($units as $unit): ?>
            <div class="row col-xs-12">
                <?= renderPhpFile('unit/unit', ['unit' => $unit]) ?>
            </div>
            <div class="row col-xs-12">
                <?= renderPhpFile('upload/upload', ['unit' => $unit]); ?>
            </div>
        <?php endforeach; ?>
        <?= renderPhpFile('unit/pagination') ?>
    <?php else: ?>
        <h3 class="text-center">Nothing found</h3>
    <?php endif; ?>
</div>

