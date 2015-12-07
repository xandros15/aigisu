<?php /** @var $errors array */ ?>

<?php foreach ($errors as $error): ?>
    <div class="alert alert-danger" role="alert">
        <?= $error ?>
    </div>
<?php
endforeach;
