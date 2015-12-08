<?php

use models\Images;

global $query;
/* @var $imageSet Images */
$imageSet = Images::imagesByUnit($query->get->image);
$images   = $imageSet->getSortedImages();
?>
<?php foreach ($images as $serverName => $server): ?>
    <ul class="list-unstyled <?= $serverName ?> col-xs-12 col-sm-6<?= ($imageSet->length > 2) ? '' : ' col-sm-offset-3' ?>">
        <h2 class="text-center">#<?= $serverName ?></h2>
        <?php foreach ($server as $scene => $image): ?>
            <li style="display: inline-block; position: relative;">
                <span style="position: absolute; top: 10px; left: 10px; font-size: 20px;">#<?= $image->id ?></span>
                <img id="<?= $image->id ?>" alt="<?= $serverName . $scene ?>" style="max-width: 100%;" src="<?= Images::createImagelink($image->id) ?>">
            </li>
        <?php endforeach; ?>
    </ul>
<?php endforeach; ?>
