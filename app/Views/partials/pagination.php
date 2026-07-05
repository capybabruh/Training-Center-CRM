<div class="pagination">
    <span>Tổng: <?= e((string)$total) ?> bản ghi</span>
    <?php if ($page > 1): ?>
        <a href="?<?= e(query_string(['page' => $page - 1])) ?>">&laquo; Trước</a>
    <?php endif; ?>
    <span>Trang <?= e((string)$page) ?> / <?= e((string)$totalPages) ?></span>
    <?php if ($page < $totalPages): ?>
        <a href="?<?= e(query_string(['page' => $page + 1])) ?>">Sau &raquo;</a>
    <?php endif; ?>
</div>
