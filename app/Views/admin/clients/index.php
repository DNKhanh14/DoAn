<div class="container-fluid">
    <div class="page-title-bar">
        <h1><i class="fas fa-users mr-2"></i>Khách hàng</h1>
        <a href="<?= admin_route('crm') ?>" class="btn-them-moi">
            <i class="fas fa-list mr-1"></i> Quản lý CRM
        </a>
    </div>

    <!-- Tổng quan nhanh -->
    <div class="row mb-4">
        <div class="col-auto">
            <div class="stat-pill">
                <i class="fas fa-users mr-1"></i>
                <strong><?= count($clients) ?></strong> khách hàng
            </div>
        </div>
    </div>

    <?php if (empty($clients)): ?>
        <div class="card card-salon shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-3">Chưa có khách hàng nào.</p>
                <a href="<?= admin_route('crm') ?>" class="btn btn-primary">
                    <i class="fas fa-user-plus mr-1"></i> Thêm khách hàng đầu tiên
                </a>
            </div>
        </div>
    <?php else: ?>
        <?php
        // Màu sắc cho cards
        $colors = ['#1e5bb8', '#e74a3b', '#1cc88a', '#f6c23e', '#6f42c1', '#fd7e14', '#20c9a6'];
        $icons = ['fa-user', 'fa-user-tie', 'fa-user-circle', 'fa-user-friends'];
        ?>

        <div class="row">
            <?php foreach ($clients as $idx => $client): ?>
                <?php 
                    $color = $colors[$idx % count($colors)];
                    $icon = $icons[$idx % count($icons)];
                    $fullName = trim(htmlspecialchars($client['ten']) . ' ' . htmlspecialchars($client['ho_dem']));
                ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm client-card" style="border-left: 4px solid <?= $color ?>; border-radius:10px; overflow:hidden; height:100%; transition: all 0.2s;">
                        <div class="card-body p-4">
                            <!-- Header với avatar -->
                            <div class="d-flex align-items-start mb-3">
                                <div class="client-avatar mr-3" style="background:<?= $color ?>1a; color:<?= $color ?>; width:55px; height:55px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1.2rem; font-weight:700">
                                    <?= strtoupper(mb_substr($client['ten'], 0, 1)) ?>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1 font-weight-bold" style="color:#2d3748; font-size:1.05rem">
                                        <?= $fullName ?>
                                    </h5>
                                    <div class="text-muted" style="font-size:0.85rem">
                                        <i class="fas fa-hashtag" style="font-size:0.75rem"></i><?= (int)$client['ma_khach_hang'] ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Thông tin liên hệ -->
                            <div class="client-info mb-3">
                                <?php if (!empty($client['so_dien_thoai'])): ?>
                                <div class="d-flex align-items-center mb-2" style="font-size:0.9rem">
                                    <i class="fas fa-phone mr-2" style="color:<?= $color ?>; width:18px"></i>
                                    <span><?= htmlspecialchars($client['so_dien_thoai']) ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($client['email'])): ?>
                                <div class="d-flex align-items-center" style="font-size:0.9rem">
                                    <i class="fas fa-envelope mr-2" style="color:<?= $color ?>; width:18px"></i>
                                    <span class="text-truncate"><?= htmlspecialchars($client['email']) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Thao tác -->
                            <div class="d-flex justify-content-end" style="gap:8px; margin-top:15px; padding-top:15px; border-top:1px solid #e2e8f0">
                                <a href="<?= admin_route('crm/detail', ['id' => $client['ma_khach_hang']]) ?>" 
                                   class="btn btn-sm" 
                                   title="Chi tiết"
                                   style="width:36px; height:36px; border-radius:8px; background:<?= $color ?>; border:none; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; text-decoration:none">
                                    <i class="fas fa-eye" style="color:#fff; font-size:14px"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.client-card {
    transition: all 0.2s ease;
}
.client-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,.12) !important;
}

.stat-pill {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 5px 14px;
    font-size: 0.85rem;
    color: #4a5568;
    display: inline-block;
}

.client-avatar {
    transition: transform 0.2s ease;
}

.client-card:hover .client-avatar {
    transform: scale(1.1);
}

@media (max-width: 768px) {
    .col-md-6 {
        margin-bottom: 1rem;
    }
}
</style>
