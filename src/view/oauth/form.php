<?php

use models\Oauth; ?>
<form class="form" method="POST" role="form">
    <?php if (!Oauth::isLogged()): ?>
        <div class="form-group text-center">
            <label>PIN</label>
            <input style="max-width: 200px; margin: auto;" type="text" class="form-control" placeholder="pin" name="pin" value="">
        </div>
    <?php else: ?>
        <input type="hidden" value="1" name="logout">
    <?php endif; ?>
    <div class="col-xs-12 text-center">
        <button class="btn btn-default" type="submit"><?= (!Oauth::isLogged()) ? 'auth' : 'logout' ?></button>
    </div>
</form>