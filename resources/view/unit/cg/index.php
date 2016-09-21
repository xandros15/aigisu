<?php

$cg = new \Illuminate\Database\Eloquent\Collection($cg);
$unit = $cg->first()['unit'];
$cg = $cg->sortBy('scene')->sortBy('server')->groupBy('server');
$this->title = 'CG ' . $unit['name'];
$this->containerClass = 'container-fluid';
?>
<?php foreach ($cg as $serverName => $server): ?>
    <ul class="list-unstyled <?= $serverName ?> col-xs-12 col-md-6">
        <h2 class="text-center">#<?= $serverName ?></h2>
        <?php foreach ($server as $image): ?>
            <?php /** @var $image \Aigisu\Api\Models\Unit\CG */ ?>
            <li style="display: inline-block; position: relative;">
                <span style="position: absolute; top: 10px; left: 10px; font-size: 20px;">#<?= $image['id'] ?></span>
                <img id="<?= $image['id'] ?>" alt="<?= $serverName . $image['scene'] ?>" style="max-width: 100%;"
                     src="<?= $image['imgur'] ?>">
            </li>
        <?php endforeach; ?>
    </ul>
<?php endforeach; ?>
