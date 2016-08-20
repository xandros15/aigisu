<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-20
 * Time: 19:26
 */
$this->title = 'Register new user';
$this->containerClass = 'container';
?>
<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">Register the new user</div>
            <div class="panel-body">
                <form method="post" action="<?= $this->pathFor('user.create'); ?>">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name">
                    </div>
                    <div class="form-group">
                        <label for="email">Email address</label>
                        <input type="email" class="form-control" id="email" placeholder="email@domain.com">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password">
                    </div>
                    <button type="submit" class="btn btn-default">Register</button>
                </form>
            </div>
        </div>
    </div>
</div>
