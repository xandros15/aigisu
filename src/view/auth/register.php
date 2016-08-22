<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-20
 * Time: 19:26
 */
/** @var $user \Models\User */
$this->title = 'Register new user';
$this->containerClass = 'container';
?>
<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">Register the new user</div>
            <div class="panel-body">
                <form id="form-to-valid" method="post" data-toggle="validator" autocomplete="off"
                      action="<?= $this->pathFor('user.create'); ?>">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" value="<?= $user->name ?>" class="form-control" id="name"
                               required pattern=".{4,15}">
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email address</label>
                        <input type="email" name="email" value="<?= $user->email ?>" class="form-control" id="email"
                               placeholder="email@domain.com" required>
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" value="<?= $user->password ?>" class="form-control"
                               id="password" required pattern=".{8,32}">
                        <div class="help-block with-errors"></div>
                    </div>
                    <button type="submit" class="btn btn-default">Register</button>
                </form>
            </div>
        </div>
    </div>
</div>
