<div class="container-fluid">

  <!-- Tiêu đề -->
  <div class="easy-page-head mb-3">
    <h1>Bản tin</h1>
    <div class="easy-page-actions">
      <a href="<?= admin_route('booking/create') ?>" class="btn btn-primary btn-sm">
        <i class="fas fa-plus mr-1"></i> Tạo lịch hẹn
      </a>
      <a href="<?= admin_route('reports') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-chart-line mr-1"></i> Báo cáo
      </a>
    </div>
  </div>

  <!-- Thẻ thống kê nhanh -->
  <div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
      <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #1a9bd7 !important">
        <div class="card-body d-flex align-items-center gap-3" style="gap:16px">
          <div style="width:44px;height:44px;border-radius:10px;background:#e8f6fd;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fas fa-calendar-check" style="color:#1a9bd7;font-size:18px"></i>
          </div>
          <div>
            <div style="font-size:0.72rem;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em">Lịch hẹn hôm nay</div>
            <div style="font-size:1.6rem;font-weight:800;color:#1e2d3d;line-height:1.1"><?= $todayBookings ?></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
      <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #16a34a !important">
        <div class="card-body d-flex align-items-center" style="gap:16px">
          <div style="width:44px;height:44px;border-radius:10px;background:#dcfce7;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fas fa-users" style="color:#16a34a;font-size:18px"></i>
          </div>
          <div>
            <div style="font-size:0.72rem;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em">Khách hàng</div>
            <div style="font-size:1.6rem;font-weight:800;color:#1e2d3d;line-height:1.1"><?= $totalClients ?></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
      <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #f59e0b !important">
        <div class="card-body d-flex align-items-center" style="gap:16px">
          <div style="width:44px;height:44px;border-radius:10px;background:#fef3c7;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fas fa-user-tie" style="color:#f59e0b;font-size:18px"></i>
          </div>
          <div>
            <div style="font-size:0.72rem;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em">Nhân viên</div>
            <div style="font-size:1.6rem;font-weight:800;color:#1e2d3d;line-height:1.1"><?= $totalEmployees ?></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
      <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #8b5cf6 !important">
        <div class="card-body d-flex align-items-center" style="gap:16px">
          <div style="width:44px;height:44px;border-radius:10px;background:#ede9fe;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fas fa-cut" style="color:#8b5cf6;font-size:18px"></i>
          </div>
          <div>
            <div style="font-size:0.72rem;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em">Dịch vụ</div>
            <div style="font-size:1.6rem;font-weight:800;color:#1e2d3d;line-height:1.1"><?= $totalServices ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Hàng biểu đồ -->
  <!-- Biểu đồ đường: Doanh thu 7 ngày -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom" style="font-weight:700;color:#1e2d3d;font-size:0.95rem">
          <i class="fas fa-chart-line mr-2" style="color:#1a9bd7"></i> Doanh thu / lịch hẹn 7 ngày gần nhất
        </div>
        <div class="card-body" style="height:260px;position:relative">
          <canvas id="chartRevenue"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Biểu đồ cột đôi -->
  <div class="row mb-4">
    <div class="col-lg-6 mb-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-bottom" style="font-weight:700;color:#1e2d3d;font-size:0.95rem">
          <i class="fas fa-chart-bar mr-2" style="color:#16a34a"></i> Dịch vụ được đặt nhiều nhất
        </div>
        <div class="card-body" style="height:260px;position:relative">
          <canvas id="chartSvc"></canvas>
        </div>
      </div>
    </div>
    <div class="col-lg-6 mb-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-bottom" style="font-weight:700;color:#1e2d3d;font-size:0.95rem">
          <i class="fas fa-chart-bar mr-2" style="color:#f59e0b"></i> Barber phục vụ nhiều nhất
        </div>
        <div class="card-body" style="height:260px;position:relative">
          <canvas id="chartBarber"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Lịch hẹn hôm nay -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
      <span style="font-weight:700;color:#1e2d3d;font-size:0.95rem">
        <i class="fas fa-calendar-day mr-2" style="color:#1a9bd7"></i>
        Lịch hẹn hôm nay
        <?php if($todayBookings > 0): ?>
          <span class="badge badge-primary ml-1"><?= $todayBookings ?></span>
        <?php endif; ?>
      </span>
      <a href="<?= admin_route('booking') ?>" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
    </div>
    <div class="card-body p-0">
      <?php if(empty($todayList)): ?>
        <div class="text-center py-5 text-muted">
          <i class="fas fa-calendar-times fa-2x d-block mb-2"></i>
          Chưa có lịch hẹn hôm nay
        </div>
      <?php else: ?>
      <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
          <thead class="thead-light">
            <tr>
              <th style="width:70px">Giờ</th>
              <th>Khách hàng</th>
              <th>Nhân viên</th>
              <th style="width:80px">Dịch vụ</th>
              <th style="width:120px">Trạng thái</th>
              <th style="width:80px"></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($todayList as $a):
              $st = strtolower(trim($a['trang_thai'] ?? ''));
              if($a['da_huy']) $st = 'cancelled';
              $stLabel = appointment_status_label($st);
              $stClass = appointment_status_class($st);
              $svcs = $appointmentModel->getBookedServices((int)$a['ma_lich_hen']);
            ?>
            <tr>
              <td class="font-weight-bold" style="color:#1a9bd7"><?= date('H:i', strtotime($a['thoi_gian_bat_dau'])) ?></td>
              <td>
                <div class="font-weight-bold"><?= htmlspecialchars(trim($a['ten'].' '.$a['ho_dem'])) ?></div>
                <small class="text-muted"><?= htmlspecialchars($a['so_dien_thoai'] ?? '') ?></small>
              </td>
              <td><?= htmlspecialchars(trim($a['emp_ten'].' '.$a['emp_ho'])) ?></td>
              <td>
                <small><?php foreach($svcs as $s): ?><?= htmlspecialchars($s['ten_dich_vu']) ?><br><?php endforeach; ?></small>
              </td>
              <td><span class="badge badge-<?= $stClass ?>"><?= $stLabel ?></span></td>
              <td>
                <a href="<?= admin_route('booking/edit', ['id' => (int)$a['ma_lich_hen']]) ?>"
                   class="btn btn-xs btn-outline-primary" style="padding:2px 8px;font-size:0.75rem">
                  Xem
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>

</div><!-- /container-fluid -->

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function(){
  'use strict';
  Chart.defaults.font.family = "'Nunito', sans-serif";
  Chart.defaults.color = '#6b7280';

  var revenueLabels = <?= json_encode($revenueLabels) ?>;
  var revenueData   = <?= json_encode($revenueData) ?>;
  var svcLabels     = <?= json_encode($svcLabels) ?>;
  var svcData       = <?= json_encode($svcData) ?>;
  var barberLabels  = <?= json_encode($barberLabels) ?>;
  var barberData    = <?= json_encode($barberData) ?>;

  /* ── Biểu đồ đường: Doanh thu ── */
  new Chart(document.getElementById('chartRevenue'), {
    type: 'line',
    data: {
      labels: revenueLabels,
      datasets: [{
        label: 'Doanh thu (VNĐ)',
        data: revenueData,
        borderColor: '#1a9bd7',
        backgroundColor: 'rgba(26,155,215,0.08)',
        borderWidth: 2.5,
        pointBackgroundColor: '#1a9bd7',
        pointRadius: 4,
        fill: true,
        tension: 0.4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: function(ctx){
              return ' ' + new Intl.NumberFormat('vi-VN').format(ctx.parsed.y) + ' VNĐ';
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(v){
              if(v >= 1000000) return (v/1000000).toFixed(1)+'M';
              if(v >= 1000) return (v/1000).toFixed(0)+'K';
              return v;
            }
          },
          grid: { color: '#f3f4f6' }
        },
        x: { grid: { display: false } }
      }
    }
  });

  /* ── Biểu đồ cột: Dịch vụ ── */
  new Chart(document.getElementById('chartSvc'), {
    type: 'bar',
    data: {
      labels: svcLabels.length ? svcLabels : ['Chưa có dữ liệu'],
      datasets: [{
        label: 'Số lượt đặt',
        data: svcData.length ? svcData : [0],
        backgroundColor: [
          'rgba(26,155,215,.75)','rgba(22,163,74,.75)','rgba(245,158,11,.75)',
          'rgba(139,92,246,.75)','rgba(239,68,68,.75)','rgba(20,184,166,.75)','rgba(249,115,22,.75)'
        ],
        borderRadius: 6,
        borderSkipped: false
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f3f4f6' } },
        x: { grid: { display: false }, ticks: { maxRotation: 25 } }
      }
    }
  });

  /* ── Biểu đồ cột: Barber ── */
  new Chart(document.getElementById('chartBarber'), {
    type: 'bar',
    data: {
      labels: barberLabels.length ? barberLabels : ['Chưa có dữ liệu'],
      datasets: [{
        label: 'Lượt phục vụ',
        data: barberData.length ? barberData : [0],
        backgroundColor: [
          'rgba(245,158,11,.75)','rgba(26,155,215,.75)','rgba(139,92,246,.75)',
          'rgba(22,163,74,.75)','rgba(239,68,68,.75)','rgba(20,184,166,.75)','rgba(249,115,22,.75)'
        ],
        borderRadius: 6,
        borderSkipped: false
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f3f4f6' } },
        x: { grid: { display: false } }
      }
    }
  });

})();
</script>
