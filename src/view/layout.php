<?php

use app\alert\Alert; ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Units</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
        <link href="./css/main.css" rel="stylesheet">
    </head>
    <body>
        <div class="<?= (isImageQuery()) ? 'container-fluid' : 'container' ?>">
            <?= Alert::display(); ?>
            <main class="row">
                <?php if (isImageQuery()): ?>
                    <?= renderPhpFile('image/images') ?>
                <?php elseif (isLoginQuery()): ?>
                    <?= renderPhpFile('oauth/index') ?>
                <?php else: ?>
                    <div class="search-form col-xs-12">
                        <?= renderPhpFile('search/form') ?>
                    </div>
                    <?= renderPhpFile('unit/units'); ?>
                <?php endif; ?>
            </main>
            <footer class="row text-center">
                <p>&copy; xandros. Images and media relating to Millennium War Aigis are property of Nutaku.net and DMM.com</p>
            </footer>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="./js/bootstrap.min.js"></script>
        <script>globalUrl = '<?= SITE_URL ?>';</script>
        <script src="./js/updateAjax.js"></script>
        <script src="./js/blockDisabledLinks.js"></script>
        <script src="./js/openImages.js"></script>
    </body>
</html>