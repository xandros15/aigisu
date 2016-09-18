<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-01
 * Time: 19:24
 */
use Aigisu\Common\Components\View\UrlExtension;

/** @var $this UrlExtension * */
?>
<div class="body-content">

    <div class="row">
        <div class="col-sm-4">
            <h2>Units</h2>

            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                fugiat nulla pariatur.</p>

            <p><a class="btn btn-default" href="<?= $this->pathFor('unit.index') ?>">Units »</a></p>
        </div>
    </div>

</div>
