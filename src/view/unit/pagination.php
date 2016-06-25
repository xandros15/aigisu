<nav class="text-center col-xs-12">
    <ul class="pagination">
        <?php /** @var $pagination Xandros15\SlimPagination\Pagination */ ?>
        <?php if ($pagination->previous()['isCurrent']): ?>
            <li class="disabled">
                <span><?= $pagination->previous()['pageName'] ?></span>
            </li>
        <?php else: ?>
            <li>
                <a aria-label="previous" href="<?= $pagination->previous()['pathFor'] ?>">
                    <span aria-hidden="true"><?= $pagination->previous()['pageName'] ?></span>
                </a>
            </li>
        <?php endif ?>
        <?php foreach ($pagination as $page): ?>
            <?php if ($page['isSlider']): ?>
                <li class="disabled">
                    <span><?= $page['pageName'] ?></span>
                </li>
            <?php elseif ($page['isCurrent']): ?>
                <li class="active">
                    <span><?= $page['pageName'] ?></span>
                </li>
            <?php else: ?>
                <li>
                    <a href="<?= $page['pathFor'] ?>"><?= $page['pageName'] ?></a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php if ($pagination->next()['isCurrent']): ?>
            <li class="disabled">
                <span><?= $pagination->next()['pageName'] ?></span>
            </li>
        <?php else: ?>
            <li>
                <a aria-label="next" href="<?= $pagination->next()['pathFor'] ?>">
                    <span aria-hidden="true"><?= $pagination->next()['pageName'] ?></span>
                </a>
            </li>
        <?php endif ?>
    </ul>
</nav>