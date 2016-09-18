<?php

use Aigisu\Common\Components\Alert\Alert;
use Aigisu\Common\Components\View\LayoutExtension;
use Aigisu\Common\Components\View\View;

/* @var $this LayoutExtension|View */
/* @var $content string */
$this->containerClass = $this->containerClass ?? 'container';
$this->title = $this->title ?? 'Aigisu';
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