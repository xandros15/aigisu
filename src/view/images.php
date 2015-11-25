<?php $classes = getImagesFromDb(); ?>
<?php foreach ($classes as $class => $images): ?>
    <ul class="list-unstyled <?= $class ?> col-xs-12 col-sm-6">
        <h2>#<?= $class ?></h2>
        <?php foreach ($images as $i => $image): ?>
            <li style="display: inline-block; position: relative;">
                <span style="position: absolute; top: 10px; left: 10px; font-size: 20px;">#<?= $image->id ?></span>
                <img id="<?= $image->id ?>" alt="<?= $class . ($i + 1) ?>" style="max-width: 100%;" src="<?= getImageFile($image) ?>">
            </li>
        <?php endforeach; ?>
    </ul>
<?php endforeach; ?>
