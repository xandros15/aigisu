<?php

use models\Images;

global $query;
$imageSet = Images::setImagesFromUnitId($query->get->image);
$labels   = $imageSet->getAllImages();
?>
<?php foreach ($labels as $label => $images): ?>
    <ul class="list-unstyled <?= $label ?> col-xs-12 col-sm-6 <?= count($labels) > 1 ? : 'col-sm-offset-3' ?>">
        <h2>#<?= $label ?></h2>
        <?php foreach ($images as $i => $image): ?>
            <li style="display: inline-block; position: relative;">
                <span style="position: absolute; top: 10px; left: 10px; font-size: 20px;">#<?= $image->id ?></span>
                <img id="<?= $image->id ?>" alt="<?= $label . ($i + 1) ?>" style="max-width: 100%;" src="<?= $imageSet->getImageLink($image->type) ?>">
            </li>
        <?php endforeach; ?>
    </ul>
<?php endforeach; ?>