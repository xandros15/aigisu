<?php

use models\Images;
use app\core\View;

/* @var $this View */
/* @var $imageSet Images */
$this->setTitle('images');
$this->setContainerClass('container-fluid');
?>
<?php foreach ($images as $serverName => $server): ?>
    <ul class="list-unstyled <?= $serverName ?> col-xs-12 col-sm-6">
        <h2 class="text-center">#<?= $serverName ?></h2>
    <?php foreach ($server as $scene => $image): ?>
            <li style="display: inline-block; position: relative;">
                <span style="position: absolute; top: 10px; left: 10px; font-size: 20px;">#<?= $image->id ?></span>
                <img id="<?= $image->id ?>" alt="<?= $serverName . $scene ?>" style="max-width: 100%;" src="<?= Images::createImagelink($image->id) ?>">
            </li>
    <?php endforeach; ?>
    </ul>
<?php endforeach; ?>
