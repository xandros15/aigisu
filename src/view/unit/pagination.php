<?php

use controller\UnitController as Unit;

/* @var $maxPages int */
$page = Unit::getPage();
?>
<?php if ($maxPages > 0): ?>
    <nav class="text-center col-xs-12">
        <ul class="pagination">
            <li<?= (1 == $page) ? ' class="disabled"' : '' ?>>
                <a href="<?= Unit::generateLink(['page' => 1]) ?>" aria-label="first">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <?php for ($i = 1; $i <= $maxPages; $i++): ?>
                <li<?= ($i == $page) ? ' class="active"' : ''; ?>><a href="<?= Unit::generateLink(['page' => $i]) ?>"><?= $i ?></a></li>
            <?php endfor; ?>
            <li<?= ($maxPages == $page) ? ' class="disabled"' : '' ?>>
                <a href="<?= Unit::generateLink(['page' => $maxPages]) ?>" aria-label="last">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
<?php endif; ?>