<?php $maxPages = getMaxPages(); ?>
<?php if ($maxPages > 0): ?>
    <nav class="text-center">
        <ul class="pagination">
            <li<?= (1 == getCurrentPage()) ? ' class="disabled"' : '' ?>>
                <a href="<?= generateLink(['page' => 1]) ?>" aria-label="first">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <?php for ($i = 1; $i <= $maxPages; $i++): ?>
                <li<?= ($i == getCurrentPage()) ? ' class="active"' : ''; ?>><a href="<?= generateLink(['page' => $i]) ?>"><?= $i ?></a></li>
            <?php endfor; ?>
            <li<?= ($maxPages == getCurrentPage()) ? ' class="disabled"' : '' ?>>
                <a href="<?= generateLink(['page' => $maxPages]) ?>" aria-label="last">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
<?php endif; ?>