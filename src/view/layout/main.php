<?php

use Aigisu\Alert\Alert;
use Aigisu\View\View;

/* @var $this View|\Aigisu\view\LayoutExtension */
/* @var $content string */
?>
<?php $this->beginPage(); ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <title><?= $this->title ?></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php $this->head() ?>
    </head>
    <body>
    <?php $this->beginBody() ?>
    <div class="wrap">
        <?= $this->render('partials/navigation') ?>
        <main class="<?= $this->containerClass ?>">
            <?= Alert::display(); ?>
            <?= $content ?>
        </main>
    </div>
    <footer class="footer text-center">
        <p>&copy; xandros. Images and media relating to Millennium War Aigis are property of Nutaku.net and DMM.com</p>
    </footer>
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage(); ?>