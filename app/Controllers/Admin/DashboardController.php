<?php

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Models\Appointment;

class DashboardController extends AdminController
{
    public function index(): void
    {
        $this->requirePermission('dashboard');
        $pageTitle = 'Bản tin';
        $db = Database::getConnection();

        // ── Thẻ thống kê nhanh ──────────────────────────────────────────────
        $totalClients      = (int) $db->query('SELECT COUNT(*) FROM khach_hang')->fetchColumn();
        $totalEmployees    = (int) $db->query('SELECT COUNT(*) FROM nhan_vien')->fetchColumn();
        $totalServices     = (int) $db->query('SELECT COUNT(*) FROM dich_vu')->fetchColumn();
        $todayBookings     = (int) $db->query(
            "SELECT COUNT(*) FROM lich_hen WHERE DATE(thoi_gian_bat_dau)=CURDATE() AND da_huy=0"
        )->fetchColumn();

        // ── Biểu đồ 1: Doanh thu 7 ngày gần nhất (từ bảng don_hang nếu có) ─
        $revenueLabels = [];
        $revenueData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-$i days"));
            $revenueLabels[] = date('d/m', strtotime($d));

            if (table_exists('don_hang')) {
                $stmt = $db->prepare(
                    "SELECT COALESCE(SUM(tong_cong),0) FROM don_hang
                     WHERE DATE(ngay_tao)=? AND trang_thai='completed'"
                );
                $stmt->execute([$d]);
                $revenueData[] = (float) $stmt->fetchColumn();
            } else {
                // Ước tính từ lịch hẹn + giá dịch vụ
                $stmt = $db->prepare(
                    "SELECT COALESCE(SUM(dv.gia),0)
                     FROM lich_hen lh
                     JOIN dich_vu_dat dvd ON lh.ma_lich_hen=dvd.ma_lich_hen
                     JOIN dich_vu dv ON dvd.ma_dich_vu=dv.ma_dich_vu
                     WHERE DATE(lh.thoi_gian_bat_dau)=? AND lh.da_huy=0"
                );
                $stmt->execute([$d]);
                $revenueData[] = (float) $stmt->fetchColumn();
            }
        }

        // ── Biểu đồ 2: Top 7 dịch vụ được đặt nhiều nhất ──────────────────
        $svcRows = $db->query(
            "SELECT dv.ten_dich_vu, COUNT(dvd.ma_dich_vu) AS cnt
             FROM dich_vu_dat dvd
             JOIN dich_vu dv ON dvd.ma_dich_vu=dv.ma_dich_vu
             JOIN lich_hen lh ON dvd.ma_lich_hen=lh.ma_lich_hen
             WHERE lh.da_huy=0
             GROUP BY dv.ma_dich_vu
             ORDER BY cnt DESC
             LIMIT 7"
        )->fetchAll();
        $svcLabels = array_column($svcRows, 'ten_dich_vu');
        $svcData   = array_map('intval', array_column($svcRows, 'cnt'));

        // ── Biểu đồ 3: Top 7 barber phục vụ nhiều khách nhất ───────────────
        $barberRows = $db->query(
            "SELECT CONCAT(nv.ten,' ',nv.ho_dem) AS ten_nv, COUNT(lh.ma_lich_hen) AS cnt
             FROM lich_hen lh
             JOIN nhan_vien nv ON lh.ma_nhan_vien=nv.ma_nhan_vien
             WHERE lh.da_huy=0
             GROUP BY lh.ma_nhan_vien
             ORDER BY cnt DESC
             LIMIT 7"
        )->fetchAll();
        $barberLabels = array_column($barberRows, 'ten_nv');
        $barberData   = array_map('intval', array_column($barberRows, 'cnt'));

        // ── Lịch hẹn hôm nay ────────────────────────────────────────────────
        $appointmentModel = new Appointment();
        $todayList = $db->query(
            "SELECT lh.*, kh.ten, kh.ho_dem, kh.so_dien_thoai,
                    nv.ten AS emp_ten, nv.ho_dem AS emp_ho
             FROM lich_hen lh
             JOIN khach_hang kh ON lh.ma_khach_hang=kh.ma_khach_hang
             JOIN nhan_vien nv ON lh.ma_nhan_vien=nv.ma_nhan_vien
             WHERE DATE(lh.thoi_gian_bat_dau)=CURDATE() AND lh.da_huy=0
             ORDER BY lh.thoi_gian_bat_dau ASC
             LIMIT 20"
        )->fetchAll();

        $this->adminView('dashboard/index', [
            'pageTitle'      => $pageTitle,
            'totalClients'   => $totalClients,
            'totalEmployees' => $totalEmployees,
            'totalServices'  => $totalServices,
            'todayBookings'  => $todayBookings,
            'revenueLabels'  => $revenueLabels,
            'revenueData'    => $revenueData,
            'svcLabels'      => $svcLabels,
            'svcData'        => $svcData,
            'barberLabels'   => $barberLabels,
            'barberData'     => $barberData,
            'todayList'      => $todayList,
            'appointmentModel' => $appointmentModel,
        ]);
    }
}
