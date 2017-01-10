<?php

use app\core\View;
use Illuminate\Database\Eloquent\Collection;
use models\Image;

/* @var $this View */
/* @var $images Collection */
/* @var $image Image */
$this->setTitle('images');
$this->setContainerClass('container-fluid');
?>
<?php foreach ($images as $serverName => $server): ?>
    <ul class="list-unstyled <?= $serverName ?> col-xs-12 col-md-6">
        <h2 class="text-center">#<?= $serverName ?></h2>
        <?php foreach ($server as $image): ?>
            <li style="display: inline-block; position: relative;">
                <span style="position: absolute; top: 10px; left: 10px; font-size: 20px;">#<?= $image->id ?></span>
                <img id="<?= $image->id ?>" alt="<?= $serverName . $image->scene ?>" style="max-width: 100%;" src="<?= $image->getLink() ?>">
            </li>
        <?php endforeach; ?>
    </ul>
<?php endforeach; ?>
