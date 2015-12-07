<?php

use models\Oauth;

$model = Oauth::load();
?>
<div style="padding: 20% 0;">
    <?php if (($errors = $model->getErrorLog())): ?>
        <?= renderPhpFile('oauth/error', ['errors' => $errors]) ?>
    <?php endif; ?>
    <?= renderPhpFile('oauth/form') ?>
</div>