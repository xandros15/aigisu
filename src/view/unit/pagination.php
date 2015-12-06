<?php

use models\Units;

/* @var $model models\Units */
?>
<?php if ($model->getMaxUnits() > 0): ?>
    <nav class="text-center">
        <ul class="pagination">
            <li<?= (1 == Units::getCurrentPage()) ? ' class="disabled"' : '' ?>>
                <a href="<?= generateLink(['page' => 1]) ?>" aria-label="first">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <?php for ($i = 1; $i <= $model->getMaxPages(); $i++): ?>
                <li<?= ($i == Units::getCurrentPage()) ? ' class="active"' : ''; ?>><a href="<?= generateLink(['page' => $i]) ?>"><?= $i ?></a></li>
            <?php endfor; ?>
            <li<?= ($model->getMaxPages() == Units::getCurrentPage()) ? ' class="disabled"' : '' ?>>
                <a href="<?= generateLink(['page' => $model->getMaxPages()]) ?>" aria-label="last">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
<?php endif; ?>