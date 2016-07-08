<?php

use Aigisu\Alert\Alert;
use Aigisu\View\View;

/* @var $this View */
/* @var $content string */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= $this->title ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/main.css" rel="stylesheet">
</head>
<body>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"
        integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS"
        crossorigin="anonymous"></script>
<script>
    $("[data-toggle=popover]").popover();
</script>
<script src="/js/blockDisabledLinks.js"></script>
<script src="/js/openImages.js"></script>
<script src="/js/polyfiller.js"></script>
<script src="/js/html5Validator.js"></script>
<script src="/js/ajax.js"></script>
</body>
</html>