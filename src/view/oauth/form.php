<?php

use models\Oauth;
use app\core\View;

/* @var $this View */
?>
<?php if (!Oauth::isLogged()): ?>
    <form class="form" method="POST" role="form" action="/oauth/login">
        <div class="form-group text-center">
            <label>PIN</label>
            <input style="max-width: 200px; margin: auto;" type="text" class="form-control" placeholder="pin" name="pin" value="">
        </div>
        <div class="col-xs-12 text-center">
            <button class="btn btn-default" type="submit">auth</button>
        </div>
    </form>
<?php else: ?>
    <form class="form" method="POST" fole="form" action="/oauth/logout">
        <div class="col-xs-12 text-center">
            <button class="btn btn-default" type="submit">logout</button>
        </div>
    </form>
<?php endif;