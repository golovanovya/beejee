<?php
$current = $pager->getPage();
$pagesCount = $pager->getPagesCount();

$urlPrev = ($current > 1) ? sprintf($route, ($current - 1)) : null;
$urlNext = ($current < $pagesCount) ? sprintf($route, ($current + 1)) : null;
$currentUrl = sprintf($route, $current);
?>

<?php if ($pagesCount > 1) : ?>
    <ul class="pagination">
        <?php if ($current > 1) : ?>
            <li class="page-item">
                <a class="page-link" href="<?= $urlPrev ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                    <span class="sr-only">Previous</span>
                </a>
            </li>
        <?php else : ?>
            <li class="page-item disabled">
                <span class="page-link" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                    <span class="sr-only">Previous</span>
                </span>
            </li>
        <?php endif; ?>

        <?php for ($page = 1; $page <= $pagesCount; $page++) : ?>
            <li class="page-item<?= ($page == $current) ? ' active' : ''?>">
                <a class="page-link" href="<?= sprintf($route, $page) ?>"><?= $page ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($current < $pagesCount) : ?>
            <li class="page-item">
                <a class="page-link" href="<?= $urlNext ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                    <span class="sr-only">Next</span>
                </a>
            </li>
        <?php else : ?>
            <li class="page-item disabled">
                <span class="page-link" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                    <span class="sr-only">Next</span>
                </span>
            </li>
        <?php endif; ?>
    </ul>
<?php endif; ?>
