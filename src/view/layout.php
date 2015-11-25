<!DOCTYPE html>
<html>
    <head>
        <title>Units</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
        <link href="<?= "http://$_SERVER[HTTP_HOST]" ?>/src/css/niceFileUpload.css" rel="stylesheet">
        <style>
            ul.images{
                margin: 0;
                padding: 0;
                list-style: none;
            }
            .images > li {
                margin: 0;
                padding: 0;
            }
            .list li{
                display: inline-block;
            }
            .container-fluid{
                min-width: 480px;
            }
            footer{
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class="container-fluid">
            <?php if (isImageQuery()): ?>
                <?= renderPhpFile('images') ?>
            <?php else: ?>
                <div class="search-form">
                    <?= renderPhpFile('searchForm') ?>
                </div>
                <?= renderPhpFile('units'); ?>
            <?php endif; ?>
            <footer><p>&copy; xandros. Images and media relating to Millennium War Aigis are property of Nutaku.net and DMM.com</p></footer>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
        <script>globalUrl = '<?= "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ?>';</script>
        <script src="/src/js/updateAjax.js"></script>
        <script src="/src/js/blockDisabledLinks.js"></script>
        <script src="/src/js/openImages.js"></script>
    </body>
</html>