<!DOCTYPE html>
<html>
    <head>
        <title>Units</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
        <link href="<?= "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ?>src/css/niceFileUpload.css" rel="stylesheet">
        <style>
            .list li{
                display: inline-block;
            }
            .container-fluid{
                min-width: 480px;
            }
        </style>
    </head>
    <body>
        <div class="container-fluid">
            <div class="search-form">
                <?= renderPhpFile('searchForm') ?>
            </div>
            <?= renderPhpFile('units'); ?>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
        <script>globalUrl = '<?= "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ?>';</script>
        <script src="/src/js/updateAjax.js"></script>
        <script src="/src/js/blockDisabledLinks.js"></script>
    </body>
</html>