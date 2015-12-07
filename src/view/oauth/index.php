<?php

use models\Oauth;

Oauth::load();
?>
<div style="padding: 20% 0;">
    <?= renderPhpFile('oauth/form') ?>
</div>