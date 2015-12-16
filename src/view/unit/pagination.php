<?php

use models\Unit;

/* @var $model models\Unit */
?>
<?php if ($model->getMaxUnits() > 0): ?>
    <nav class="text-center row col-xs-12">
        <ul class="pagination">
            <li<?= (1 == Unit::getCurrentPage()) ? ' class="disabled"' : '' ?>>
                <a href="<?= generateLink(['page' => 1]) ?>" aria-label="first">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <?php for ($i = 1; $i <= $model->getMaxPages(); $i++): ?>
                <li<?= ($i == Unit::getCurrentPage()) ? ' class="active"' : ''; ?>><a href="<?= generateLink(['page' => $i]) ?>"><?= $i ?></a></li>
            <?php endfor; ?>
            <li<?= ($model->getMaxPages() == Unit::getCurrentPage()) ? ' class="disabled"' : '' ?>>
                <a href="<?= generateLink(['page' => $model->getMaxPages()]) ?>" aria-label="last">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
<?php endif; ?>