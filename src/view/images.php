<?php $images = getImagesFromDb(); ?>
<ul class="images">
    <?php foreach ($images as $image): ?>
        <li><img alt="image" src="<?= getImageFile($image) ?>"></li>
    <?php endforeach; ?>
</ul>
