<?php

/**
 * Helper phân trang dùng chung toàn dự án
 */

/**
 * Tính toán thông số phân trang
 *
 * @param int $totalItems  Tổng số bản ghi
 * @param int $page        Trang hiện tại (từ GET)
 * @param int $perPage     Số bản ghi mỗi trang
 * @return array{page: int, perPage: int, totalPages: int, offset: int, from: int, to: int}
 */
function paginate(int $totalItems, int $page = 1, int $perPage = 20): array
{
    $perPage    = max(5, min(100, $perPage));
    $totalPages = $totalItems > 0 ? (int) ceil($totalItems / $perPage) : 1;
    $page       = max(1, min($page, $totalPages));
    $offset     = ($page - 1) * $perPage;
    $from       = $totalItems > 0 ? $offset + 1 : 0;
    $to         = min($offset + $perPage, $totalItems);

    return compact('page', 'perPage', 'totalPages', 'offset', 'from', 'to');
}

/**
 * Render HTML phân trang đúng thiết kế
 *
 * @param array  $pag       Kết quả từ paginate()
 * @param string $baseUrl   URL gốc (không có page/per_page)
 * @param array  $extra     Các query param bổ sung (ví dụ: ['q' => 'search'])
 */
function render_pagination(array $pag, string $baseUrl, array $extra = []): string
{
    if ($pag['totalPages'] <= 1) {
        // Vẫn hiển thị thông tin "Hiển thị" ngay cả khi 1 trang
    }

    $page       = $pag['page'];
    $totalPages = $pag['totalPages'];
    $perPage    = $pag['perPage'];
    $from       = $pag['from'];
    $to         = $pag['to'];
    $total      = $pag['total'] ?? ($pag['from'] + $pag['to'] - 1); // fallback

    // Xây dựng URL helper
    $buildUrl = static function (int $p, int $pp) use ($baseUrl, $extra): string {
        $params = array_merge($extra, ['page' => $p, 'per_page' => $pp]);
        $sep    = str_contains($baseUrl, '?') ? '&' : '?';
        return $baseUrl . $sep . http_build_query($params);
    };

    // Tính dãy số trang hiển thị (window ±2 quanh trang hiện tại)
    $window = 2;
    $pages  = [];
    for ($i = max(1, $page - $window); $i <= min($totalPages, $page + $window); $i++) {
        $pages[] = $i;
    }

    $perPageOptions = [10, 20, 50, 100];

    ob_start();
    ?>
    <div class="pagination-wrap d-flex align-items-center justify-content-between flex-wrap" style="gap:8px">

        <!-- Thông tin hiển thị -->
        <div class="pagination-info text-muted" style="font-size:0.85rem; white-space:nowrap">
            Hiển thị từ <strong><?= $from ?></strong> đến <strong><?= $to ?></strong>
            trên tổng số <strong><?= $pag['total'] ?? '?' ?></strong>
        </div>

        <div class="d-flex align-items-center" style="gap:6px">

            <!-- Nút trang trước -->
            <?php if ($page > 1): ?>
                <a href="<?= htmlspecialchars($buildUrl($page - 1, $perPage)) ?>"
                   class="btn-page" aria-label="Trang trước">
                    <i class="fas fa-chevron-left" style="font-size:0.75rem"></i>
                </a>
            <?php else: ?>
                <span class="btn-page disabled">
                    <i class="fas fa-chevron-left" style="font-size:0.75rem"></i>
                </span>
            <?php endif; ?>

            <!-- Trang đầu nếu xa -->
            <?php if ($pages[0] > 1): ?>
                <a href="<?= htmlspecialchars($buildUrl(1, $perPage)) ?>" class="btn-page">1</a>
                <?php if ($pages[0] > 2): ?>
                    <span class="btn-page disabled" style="border:none;background:none">…</span>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Dãy số trang -->
            <?php foreach ($pages as $p): ?>
                <?php if ($p === $page): ?>
                    <span class="btn-page active"><?= $p ?></span>
                <?php else: ?>
                    <a href="<?= htmlspecialchars($buildUrl($p, $perPage)) ?>" class="btn-page"><?= $p ?></a>
                <?php endif; ?>
            <?php endforeach; ?>

            <!-- Trang cuối nếu xa -->
            <?php if (end($pages) < $totalPages): ?>
                <?php if (end($pages) < $totalPages - 1): ?>
                    <span class="btn-page disabled" style="border:none;background:none">…</span>
                <?php endif; ?>
                <a href="<?= htmlspecialchars($buildUrl($totalPages, $perPage)) ?>" class="btn-page"><?= $totalPages ?></a>
            <?php endif; ?>

            <!-- Nút trang sau -->
            <?php if ($page < $totalPages): ?>
                <a href="<?= htmlspecialchars($buildUrl($page + 1, $perPage)) ?>"
                   class="btn-page" aria-label="Trang sau">
                    <i class="fas fa-chevron-right" style="font-size:0.75rem"></i>
                </a>
            <?php else: ?>
                <span class="btn-page disabled">
                    <i class="fas fa-chevron-right" style="font-size:0.75rem"></i>
                </span>
            <?php endif; ?>

            <!-- Dropdown số bản ghi / trang -->
            <div class="dropdown ml-2">
                <button class="btn-page dropdown-toggle" data-toggle="dropdown" style="min-width:90px; padding:0 10px">
                    <?= $perPage ?> / trang
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow-sm" style="min-width:120px">
                    <?php foreach ($perPageOptions as $opt): ?>
                        <a class="dropdown-item <?= $opt === $perPage ? 'active' : '' ?>"
                           href="<?= htmlspecialchars($buildUrl(1, $opt)) ?>"
                           style="font-size:0.85rem">
                            <?= $opt ?> / trang
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>

    <style>
    .pagination-wrap { margin-top: 16px; }
    .btn-page {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 34px;
        padding: 0 8px;
        border: 1.5px solid #dee2e6;
        border-radius: 6px;
        background: #fff;
        color: #4a5568;
        font-size: 0.85rem;
        font-weight: 500;
        text-decoration: none;
        transition: all .15s;
        cursor: pointer;
        white-space: nowrap;
    }
    .btn-page:hover:not(.disabled):not(.active) {
        background: #f0f4ff;
        border-color: #1e5bb8;
        color: #1e5bb8;
        text-decoration: none;
    }
    .btn-page.active {
        background: #1e5bb8;
        border-color: #1e5bb8;
        color: #fff;
        font-weight: 700;
    }
    .btn-page.disabled {
        color: #adb5bd;
        cursor: default;
        pointer-events: none;
    }
    .btn-page.dropdown-toggle::after { margin-left: 6px; }
    </style>
    <?php
    return ob_get_clean();
}
