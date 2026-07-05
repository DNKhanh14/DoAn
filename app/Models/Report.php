<?php

namespace App\Models;

use App\Core\Model;

class Report extends Model
{
    public function revenueBetween(string $from, string $to): float
    {
        if (!table_exists('don_hang')) {
            return 0;
        }

        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(tong_cong),0) FROM don_hang WHERE DATE(ngay_tao) BETWEEN ? AND ? AND trang_thai = ?'
        );
        $stmt->execute([$from, $to, 'completed']);

        return (float) $stmt->fetchColumn();
    }

    public function revenueByDay(string $from, string $to): array
    {
        $stmt = $this->db->prepare(
            'SELECT DATE(ngay_tao) AS d, SUM(tong_cong) AS revenue, COUNT(*) AS orders_count
             FROM don_hang WHERE DATE(ngay_tao) BETWEEN ? AND ? AND trang_thai = ?
             GROUP BY DATE(ngay_tao) ORDER BY d'
        );
        $stmt->execute([$from, $to, 'completed']);

        return $stmt->fetchAll();
    }

    public function topServices(int $limit = 5): array
    {
        $stmt = $this->db->prepare(
            "SELECT ten, SUM(so_luong) AS qty, SUM(tong_dong) AS revenue
             FROM chi_tiet_don_hang
             WHERE ma_dich_vu IS NOT NULL
             GROUP BY ten ORDER BY revenue DESC LIMIT ?"
        );
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function appointmentsByStatus(): array
    {
        if (!salon_upgrade_required()) {
            $stmt = $this->db->query(
                'SELECT trang_thai, COUNT(*) AS cnt FROM lich_hen GROUP BY trang_thai'
            );

            return $stmt->fetchAll();
        }

        return [];
    }

    public function employeePerformance(): array
    {
        $stmt = $this->db->query(
            'SELECT e.ma_nhan_vien, e.ten, e.ho_dem,
                    COUNT(a.ma_lich_hen) AS appt_count
             FROM nhan_vien e
             LEFT JOIN lich_hen a ON e.ma_nhan_vien = a.ma_nhan_vien AND a.da_huy = 0
             GROUP BY e.ma_nhan_vien
             ORDER BY appt_count DESC'
        );

        return $stmt->fetchAll();
    }

    public function invoiceSummary(string $from, string $to): array
    {
        if (!table_exists('don_hang')) {
            return ['count' => 0, 'revenue' => 0, 'paid' => 0, 'debt' => 0];
        }

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS cnt,
                    COALESCE(SUM(tong_truoc_giam), 0) AS revenue,
                    COALESCE(SUM(tong_cong), 0) AS paid,
                    COALESCE(SUM(giam_gia), 0) AS discount_total
             FROM don_hang
             WHERE DATE(ngay_tao) BETWEEN ? AND ? AND trang_thai = 'completed'"
        );
        $stmt->execute([$from, $to]);
        $row = $stmt->fetch() ?: [];

        return [
            'count' => (int) ($row['cnt'] ?? 0),
            'revenue' => (float) ($row['revenue'] ?? 0),
            'paid' => (float) ($row['paid'] ?? 0),
            'debt' => 0,
            'discount_total' => (float) ($row['discount_total'] ?? 0),
        ];
    }

    public function ordersBetween(string $from, string $to, int $limit = 100): array
    {
        if (!table_exists('don_hang')) {
            return [];
        }

        $stmt = $this->db->prepare(
            "SELECT o.ma_don_hang,
                    CONCAT('HD', LPAD(o.ma_don_hang, 6, '0')) AS ma_don,
                    o.ngay_tao, o.tong_truoc_giam, o.giam_gia, o.tong_cong, o.phuong_thuc_thanh_toan,
                    c.ten, c.ho_dem
             FROM don_hang o
             LEFT JOIN khach_hang c ON o.ma_khach_hang = c.ma_khach_hang
             WHERE DATE(o.ngay_tao) BETWEEN ? AND ? AND o.trang_thai = 'completed'
             ORDER BY o.ngay_tao DESC
             LIMIT ?"
        );
        $stmt->bindValue(1, $from);
        $stmt->bindValue(2, $to);
        $stmt->bindValue(3, $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /** Báo cáo hoa hồng nhân viên từ hóa đơn đã thanh toán */
    public function employeeCommissions(string $from, string $to): array
    {
        return (new Hr())->estimateCommissions($from, $to);
    }

    /** Giao dịch thu từ hóa đơn */
    public function incomeTransactions(string $from, string $to, int $limit = 100): array
    {
        if (!table_exists('don_hang')) {
            return [];
        }

        $stmt = $this->db->prepare(
            "SELECT o.ma_don_hang, o.ngay_tao, o.tong_cong, o.phuong_thuc_thanh_toan, o.ghi_chu,
                    c.ten, c.ho_dem
             FROM don_hang o
             LEFT JOIN khach_hang c ON o.ma_khach_hang = c.ma_khach_hang
             WHERE DATE(o.ngay_tao) BETWEEN ? AND ? AND o.trang_thai = 'completed'
             ORDER BY o.ngay_tao DESC
             LIMIT ?"
        );
        $stmt->bindValue(1, $from);
        $stmt->bindValue(2, $to);
        $stmt->bindValue(3, $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /** Giao dịch chi từ bảng expenses */
    public function expenseTransactions(string $from, string $to, int $limit = 100): array
    {
        if (!table_exists('chi_phi')) {
            return [];
        }

        $stmt = $this->db->prepare(
            'SELECT * FROM chi_phi
             WHERE ngay_chi BETWEEN ? AND ?
             ORDER BY ngay_chi DESC, ma_chi_phi DESC
             LIMIT ?'
        );
        $stmt->bindValue(1, $from);
        $stmt->bindValue(2, $to);
        $stmt->bindValue(3, $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /** Khách hàng có phát sinh đơn trong kỳ */
    public function topCustomers(string $from, string $to, int $limit = 50): array
    {
        if (!table_exists('don_hang')) {
            return [];
        }

        $stmt = $this->db->prepare(
            "SELECT c.ma_khach_hang, c.ten, c.ho_dem, c.so_dien_thoai,
                    COUNT(o.ma_don_hang) AS order_count,
                    COALESCE(SUM(o.tong_cong), 0) AS total_spent
             FROM don_hang o
             INNER JOIN khach_hang c ON o.ma_khach_hang = c.ma_khach_hang
             WHERE DATE(o.ngay_tao) BETWEEN ? AND ? AND o.trang_thai = 'completed'
             GROUP BY c.ma_khach_hang
             ORDER BY total_spent DESC
             LIMIT ?"
        );
        $stmt->bindValue(1, $from);
        $stmt->bindValue(2, $to);
        $stmt->bindValue(3, $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /** Thống kê thuế VAT theo ngày / tháng / năm (giá đã bao gồm thuế) */
    public function taxStatistics(string $from, string $to, string $groupBy, float $taxRate = 10.0): array
    {
        if (!table_exists('don_hang')) {
            return ['rows' => [], 'total_revenue' => 0, 'total_tax' => 0, 'total_orders' => 0];
        }

        $groupBy = in_array($groupBy, ['day', 'month', 'year'], true) ? $groupBy : 'day';
        $format = match ($groupBy) {
            'month' => '%Y-%m',
            'year' => '%Y',
            default => '%Y-%m-%d',
        };

        $stmt = $this->db->prepare(
            "SELECT DATE_FORMAT(ngay_tao, '{$format}') AS period,
                    COUNT(*) AS order_count,
                    COALESCE(SUM(tong_cong), 0) AS revenue
             FROM don_hang
             WHERE DATE(ngay_tao) BETWEEN ? AND ? AND trang_thai = 'completed'
             GROUP BY period
             ORDER BY period ASC"
        );
        $stmt->execute([$from, $to]);
        $rows = $stmt->fetchAll();

        $totalRevenue = 0.0;
        $totalTax = 0.0;
        $totalOrders = 0;

        foreach ($rows as &$row) {
            $revenue = (float) $row['revenue'];
            $tax = round($revenue * $taxRate / (100 + $taxRate), 0);
            $row['revenue'] = $revenue;
            $row['tax_amount'] = $tax;
            $row['net_revenue'] = $revenue - $tax;
            $row['order_count'] = (int) $row['order_count'];
            $totalRevenue += $revenue;
            $totalTax += $tax;
            $totalOrders += (int) $row['order_count'];
        }
        unset($row);

        return [
            'rows' => $rows,
            'total_revenue' => $totalRevenue,
            'total_tax' => $totalTax,
            'total_orders' => $totalOrders,
        ];
    }

    public static function formatTaxPeriod(string $period, string $groupBy): string
    {
        if ($groupBy === 'year') {
            return 'Năm ' . $period;
        }
        if ($groupBy === 'month' && preg_match('/^(\d{4})-(\d{2})$/', $period, $m)) {
            return 'Tháng ' . $m[2] . '/' . $m[1];
        }
        if ($groupBy === 'day' && preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $period, $m)) {
            return $m[3] . '/' . $m[2] . '/' . $m[1];
        }

        return $period;
    }

    /** Tồn kho sản phẩm */
    public function inventorySummary(int $limit = 100): array
    {
        if (!table_exists('san_pham')) {
            return [];
        }

        $stmt = $this->db->prepare(
            'SELECT ma_san_pham, ten_san_pham, so_luong_ton, so_luong_toi_thieu, gia_ban, don_vi
             FROM san_pham
             WHERE hoat_dong = 1
             ORDER BY ten_san_pham
             LIMIT ?'
        );
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
