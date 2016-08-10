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
        <link href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.min.css" rel="stylesheet">
        <link href="/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="/css/main.css" rel="stylesheet">
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
    <script src="/bower_components/jquery/dist/jquery.min.js"></script>
    <script src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script>
        $("[data-toggle=popover]").popover();
    </script>
    <script src="/js/blockDisabledLinks.js"></script>
    <script src="/js/openImages.js"></script>
    <script src="/bower_components/webshim/js-webshim/minified/polyfiller.js"></script>
    <script src="/js/html5Validator.js"></script>
    <script src="/js/ajax.js"></script>
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage(); ?>