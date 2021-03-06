<?php

use app\alert\Alert;
use app\core\View;

/* @var $this View */
/* @var $content string */
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?= $this->title ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.min.css" rel="stylesheet" >
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
        <link href="<?= $this->getBaseUrl() ?>css/main.css" rel="stylesheet">
    </head>
    <body>
        <div class="wrap">
            <nav class="navbar navbar-default navbar-inverse navbar-static-top">
                <?= $this->render('search/form') ?>
            </nav>
            <main class="<?= $this->containerClass ?>">
                <?= Alert::display(); ?>
                <?= $content ?>
            </main>
        </div>
        <footer class="footer text-center">
            <p>&copy; xandros. Images and media relating to Millennium War Aigis are property of Nutaku.net and DMM.com</p>
        </footer>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="<?= $this->getBaseUrl() ?>js/bootstrap.min.js"></script>
        <script>
            globalUrl = '<?= $this->getBaseUrl() ?>';
            $("[data-toggle=popover]").popover();
        </script>
        <script src="<?= $this->getBaseUrl() ?>js/blockDisabledLinks.js"></script>
        <script src="<?= $this->getBaseUrl() ?>js/openImages.js"></script>
        <script src="<?= $this->getBaseUrl() ?>js/polyfiller.js"></script>
        <script src="<?= $this->getBaseUrl() ?>js/html5Validator.js"></script>
        <script src="<?= $this->getBaseUrl() ?>js/ajax.js"></script>
    </body>
</html>