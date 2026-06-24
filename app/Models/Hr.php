<?php

namespace App\Models;

use App\Core\Model;

class Hr extends Model
{
    public static function bonusCategories(): array
    {
        return [
            'bonus' => ['KPI tháng', 'Thưởng doanh thu', 'Thưởng dịch vụ', 'Thưởng khác'],
            'penalty' => ['Đi muộn', 'Vi phạm nội quy', 'Làm hỏng đồ', 'Phạt khác'],
        ];
    }

    public function getAttendance(string $from, string $to): array
    {
        $stmt = $this->db->prepare(
            'SELECT ea.*, e.ten, e.ho_dem FROM cham_cong ea
             INNER JOIN nhan_vien e ON ea.ma_nhan_vien = e.ma_nhan_vien
             WHERE ea.ngay_lam_viec BETWEEN ? AND ? ORDER BY ea.ngay_lam_viec DESC'
        );
        $stmt->execute([$from, $to]);

        return $stmt->fetchAll();
    }

    public function countWorkingDays(int $employeeId, string $from, string $to): int
    {
        if (!table_exists('cham_cong')) {
            return 0;
        }
        $stmt = $this->db->prepare(
            'SELECT COUNT(DISTINCT ngay_lam_viec) FROM cham_cong
             WHERE ma_nhan_vien = ? AND ngay_lam_viec BETWEEN ? AND ?'
        );
        $stmt->execute([$employeeId, $from, $to]);

        return (int) $stmt->fetchColumn();
    }

    public function addAttendance(array $data): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO cham_cong (ma_nhan_vien, ngay_lam_viec, gio_bat_dau_ca, gio_ket_thuc_ca, loai_vai_tro, ghi_chu)
             VALUES (?,?,?,?,?,?)'
        );
        $stmt->execute([
            $data['ma_nhan_vien'], $data['ngay_lam_viec'], $data['gio_bat_dau_ca'],
            $data['gio_ket_thuc_ca'], $data['loai_vai_tro'], $data['ghi_chu'] ?? null,
        ]);
    }

    public function hasAttendanceToday(int $employeeId): bool
    {
        if (!table_exists('cham_cong') || $employeeId <= 0) {
            return false;
        }
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM cham_cong WHERE ma_nhan_vien = ? AND ngay_lam_viec = CURDATE()'
        );
        $stmt->execute([$employeeId]);

        return (int) $stmt->fetchColumn() > 0;
    }

    /** none | open | closed */
    public function getTodayAttendanceStatus(int $employeeId): string
    {
        if (!table_exists('cham_cong') || $employeeId <= 0) {
            return 'none';
        }
        $stmt = $this->db->prepare(
            'SELECT gio_bat_dau_ca, gio_ket_thuc_ca FROM cham_cong
             WHERE ma_nhan_vien = ? AND ngay_lam_viec = CURDATE() LIMIT 1'
        );
        $stmt->execute([$employeeId]);
        $row = $stmt->fetch();
        if (!$row) {
            return 'none';
        }
        if ($this->isShiftOpen($row['gio_bat_dau_ca'], $row['gio_ket_thuc_ca'])) {
            return 'open';
        }

        return 'closed';
    }

    private function isShiftOpen($start, $end): bool
    {
        $start = substr((string) $start, 0, 8);
        $end = substr((string) $end, 0, 8);

        return $start === $end;
    }

    /** Ca hôm nay chưa chốt ra */
    public function getOpenAttendanceToday(int $employeeId): ?array
    {
        if (!table_exists('cham_cong')) {
            return null;
        }
        $stmt = $this->db->prepare(
            'SELECT * FROM cham_cong
             WHERE ma_nhan_vien = ? AND ngay_lam_viec = CURDATE() LIMIT 1'
        );
        $stmt->execute([$employeeId]);
        $row = $stmt->fetch();
        if (!$row || !$this->isShiftOpen($row['gio_bat_dau_ca'], $row['gio_ket_thuc_ca'])) {
            return null;
        }

        return $row;
    }

    /** Vào ca — một lần mỗi ngày */
    public function clockIn(int $employeeId, string $roleType = 'main', ?string $note = null): void
    {
        if ($employeeId <= 0) {
            throw new \RuntimeException('Chọn nhân viên.');
        }
        if ($this->hasAttendanceToday($employeeId)) {
            throw new \RuntimeException('Đã chấm vào ca hôm nay. Mỗi ngày chỉ được vào ca một lần.');
        }
        $now = date('H:i:s');
        $this->addAttendance([
            'ma_nhan_vien' => $employeeId,
            'ngay_lam_viec' => date('Y-m-d'),
            'gio_bat_dau_ca' => $now,
            'gio_ket_thuc_ca' => $now,
            'loai_vai_tro' => $roleType,
            'ghi_chu' => $note,
        ]);
    }

    /** Ra ca — một lần mỗi ngày */
    public function clockOut(int $employeeId): void
    {
        if ($employeeId <= 0) {
            throw new \RuntimeException('Chọn nhân viên.');
        }
        $open = $this->getOpenAttendanceToday($employeeId);
        if (!$open) {
            if ($this->hasAttendanceToday($employeeId)) {
                throw new \RuntimeException('Đã chốt ra ca hôm nay. Mỗi ngày chỉ được ra ca một lần.');
            }
            throw new \RuntimeException('Chưa chấm vào ca hôm nay.');
        }
        $stmt = $this->db->prepare(
            'UPDATE cham_cong SET gio_ket_thuc_ca = ? WHERE ma_cham_cong = ?'
        );
        $stmt->execute([date('H:i:s'), (int) $open['ma_cham_cong']]);
    }

    public function estimateCommissions(string $from, string $to): array
    {
        if (!table_exists('chi_tiet_don_hang') || !table_exists('don_hang')) {
            return [];
        }

        $stmt = $this->db->prepare(
            "SELECT e.ma_nhan_vien, e.ten, e.ho_dem,
                    COALESCE(SUM(CASE WHEN oi.loai = 'service' THEN oi.tong_dong ELSE 0 END), 0) AS service_revenue,
                    COALESCE(SUM(CASE WHEN oi.loai = 'product' THEN oi.tong_dong ELSE 0 END), 0) AS product_revenue
             FROM nhan_vien e
             LEFT JOIN chi_tiet_don_hang oi ON oi.ma_nhan_vien = e.ma_nhan_vien
             LEFT JOIN don_hang o ON o.ma_don_hang = oi.ma_don_hang
                AND DATE(o.ngay_tao) BETWEEN ? AND ?
                AND o.trang_thai = 'completed'
             GROUP BY e.ma_nhan_vien
             ORDER BY e.ten, e.ho_dem"
        );
        $stmt->execute([$from, $to]);
        $rows = $stmt->fetchAll();

        $commissionRate = new CommissionRate();
        foreach ($rows as &$row) {
            $commission = 0.0;
            $employeeId = (int) $row['ma_nhan_vien'];

            // Tính hoa hồng cho từng dòng hóa đơn của nhân viên
            $itemStmt = $this->db->prepare(
                "SELECT oi.*
                 FROM chi_tiet_don_hang oi
                 INNER JOIN don_hang o ON o.ma_don_hang = oi.ma_don_hang
                 WHERE oi.ma_nhan_vien = ?
                   AND DATE(o.ngay_tao) BETWEEN ? AND ?
                   AND o.trang_thai = 'completed'"
            );
            $itemStmt->execute([$employeeId, $from, $to]);
            $items = $itemStmt->fetchAll();

            foreach ($items as $item) {
                $commission += $this->commissionForItem($item);
            }

            $row['commission_est'] = round($commission, 0);
        }

        return $rows;
    }

    public function getEmployeeCommission(int $employeeId, string $from, string $to): float
    {
        foreach ($this->estimateCommissions($from, $to) as $row) {
            if ((int) $row['ma_nhan_vien'] === $employeeId) {
                return (float) ($row['commission_est'] ?? 0);
            }
        }

        return 0.0;
    }

    /** Tính hoa hồng cho một dòng hóa đơn */
    public function commissionForItem(array $item, ?array $rule = null): float
    {
        if (empty($item['ma_nhan_vien'])) {
            return 0.0;
        }

        $lineTotal = (float) ($item['tong_dong'] ?? 0);
        $employeeId = (int) ($item['ma_nhan_vien'] ?? 0);

        // Chuẩn bị dữ liệu cho resolvePercent
        $itemForResolve = [
            'item_type' => $item['loai'] ?? '',
            'ref_id' => (int) ($item['ma_tham_chieu'] ?? 0),
        ];

        $percent = (new CommissionRate())->resolvePercent($itemForResolve, $employeeId);

        return round($lineTotal * ($percent / 100), 0);
    }

    public function getLeaveSummaryForEmployees(string $from, string $to): array
    {
        if (!table_exists('nghi_phep')) {
            return [];
        }

        $stmt = $this->db->prepare(
            "SELECT ma_nhan_vien,
                    COALESCE(SUM(CASE WHEN trang_thai = 'co_phep' THEN so_ngay_nghi ELSE 0 END), 0) AS authorized_days,
                    COALESCE(SUM(CASE WHEN trang_thai = 'khong_phep' THEN so_ngay_nghi ELSE 0 END), 0) AS unauthorized_days,
                    COALESCE(SUM(so_ngay_nghi), 0) AS total_days
             FROM nghi_phep
             WHERE den_ngay >= ? AND tu_ngay <= ?
             GROUP BY ma_nhan_vien"
        );
        $stmt->execute([$from, $to]);
        $map = [];
        foreach ($stmt->fetchAll() as $row) {
            $map[(int) $row['ma_nhan_vien']] = $row;
        }

        return $map;
    }

    public function getLeaveRecords(int $employeeId, string $from, string $to): array
    {
        if (!table_exists('nghi_phep')) {
            return [];
        }

        $stmt = $this->db->prepare(
            'SELECT * FROM nghi_phep
             WHERE ma_nhan_vien = ? AND den_ngay >= ? AND tu_ngay <= ?
             ORDER BY tu_ngay DESC'
        );
        $stmt->execute([$employeeId, $from, $to]);

        return $stmt->fetchAll();
    }

    public function addLeave(array $data): void
    {
        $tuNgay  = $data['date_from'] ?? $data['tu_ngay']  ?? date('Y-m-d');
        $denNgay = $data['date_to']   ?? $data['den_ngay'] ?? date('Y-m-d');
        $days    = $this->calculateLeaveDays($tuNgay, $denNgay, false);
        // is_authorized=true → trang_thai='co_phep'; false → 'khong_phep'
        $trangThai = empty($data['is_unauthorized']) ? 'co_phep' : 'khong_phep';

        $stmt = $this->db->prepare(
            'INSERT INTO nghi_phep (ma_nhan_vien, tu_ngay, den_ngay, so_ngay_nghi, trang_thai, ghi_chu)
             VALUES (?,?,?,?,?,?)'
        );
        $stmt->execute([
            $data['ma_nhan_vien'],
            $tuNgay,
            $denNgay,
            $days,
            $trangThai,
            $data['ghi_chu'] ?? $data['note'] ?? null,
        ]);
    }

    public function calculateLeaveDays(string $from, string $to, bool $halfDay): float
    {
        $start = new \DateTime($from);
        $end = new \DateTime($to);
        if ($end < $start) {
            [$start, $end] = [$end, $start];
        }
        $days = (int) $start->diff($end)->days + 1;
        if ($days === 1 && $halfDay) {
            return 0.5;
        }

        return (float) $days;
    }

    public function getBonusPenaltyRecords(int $employeeId, string $from, string $to): array
    {
        if (!table_exists('thuong_phat')) {
            return [];
        }

        $stmt = $this->db->prepare(
            'SELECT * FROM thuong_phat
             WHERE ma_nhan_vien = ? AND ngay_ghi BETWEEN ? AND ?
             ORDER BY ngay_ghi DESC, ma DESC'
        );
        $stmt->execute([$employeeId, $from, $to]);

        return $stmt->fetchAll();
    }

    public function addBonusPenalty(array $data): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO thuong_phat (ma_nhan_vien, ngay_ghi, loai_ghi, danh_muc, so_tien, ghi_chu)
             VALUES (?,?,?,?,?,?)'
        );
        $stmt->execute([
            $data['ma_nhan_vien'],
            $data['ngay_ghi'],
            $data['loai_ghi'] === 'penalty' ? 'penalty' : 'bonus',
            $data['category'],
            (float) $data['amount'],
            $data['ghi_chu'] ?? null,
        ]);
    }

    public function sumBonusPenalty(int $employeeId, string $from, string $to): array
    {
        if (!table_exists('thuong_phat')) {
            return ['bonus' => 0.0, 'penalty' => 0.0];
        }

        $stmt = $this->db->prepare(
            "SELECT loai_ghi, COALESCE(SUM(so_tien), 0) AS total
             FROM thuong_phat
             WHERE ma_nhan_vien = ? AND ngay_ghi BETWEEN ? AND ?
             GROUP BY loai_ghi"
        );
        $stmt->execute([$employeeId, $from, $to]);
        $result = ['bonus' => 0.0, 'penalty' => 0.0];
        foreach ($stmt->fetchAll() as $row) {
            $result[$row['loai_ghi']] = (float) $row['total'];
        }

        return $result;
    }

    public function getPayments(int $employeeId, string $from, string $to): array
    {
        if (!table_exists('thanh_toan_luong')) {
            return [];
        }

        $stmt = $this->db->prepare(
            'SELECT * FROM thanh_toan_luong
             WHERE ma_nhan_vien = ? AND ngay_thanh_toan BETWEEN ? AND ?
             ORDER BY ngay_thanh_toan DESC, ma_thanh_toan DESC'
        );
        $stmt->execute([$employeeId, $from, $to]);

        return $stmt->fetchAll();
    }

    public function addPayment(array $data): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO thanh_toan_luong (ma_nhan_vien, loai_thanh_toan, phuong_thuc, so_tien, ky_luong, ghi_chu, ngay_thanh_toan)
             VALUES (?,?,?,?,?,?,?)'
        );
        $stmt->execute([
            $data['ma_nhan_vien'],
            $data['loai_thanh_toan'],
            $data['phuong_thuc'] ?? 'cash',
            (float) $data['amount'],
            $data['salary_period'] ?? null,
            $data['ghi_chu'] ?? null,
            $data['ngay_thanh_toan'],
        ]);

        $this->syncPaymentToExpense($data);
    }

    /** Ghi thanh toán lương vào bảng chi phí → hiện tab Thu chi báo cáo */
    private function syncPaymentToExpense(array $data): void
    {
        if (!table_exists('chi_phi')) {
            return;
        }

        $employeeId = (int) ($data['ma_nhan_vien'] ?? 0);
        $emp = (new Employee())->find($employeeId);
        $empName = $emp
            ? trim(($emp['ten'] ?? '') . ' ' . ($emp['ho_dem'] ?? ''))
            : 'Nhân viên #' . $employeeId;

        $typeMap = [
            'advance' => 'Tạm ứng lương',
            'salary' => 'Thanh toán lương',
            'salary_balance' => 'Thanh toán lương tồn',
            'revenue_bonus' => 'Thưởng doanh thu',
        ];
        $paymentType = $data['loai_thanh_toan'] ?? 'salary';
        $category = $typeMap[$paymentType] ?? 'Chi lương nhân viên';
        $method = $data['phuong_thuc'] ?? 'cash';
        $noteParts = ['NV: ' . $empName];
        if (!empty($data['note'])) {
            $noteParts[] = $data['note'];
        }
        $noteParts[] = '[pm:' . $method . ']';

        (new Expense())->add(
            $category,
            (float) $data['amount'],
            $data['ngay_thanh_toan'] ?? date('Y-m-d'),
            implode('. ', $noteParts)
        );
    }

    public function sumPayments(int $employeeId, string $from, string $to): array
    {
        if (!table_exists('thanh_toan_luong')) {
            return ['advance' => 0.0, 'salary' => 0.0, 'salary_balance' => 0.0, 'revenue_bonus' => 0.0];
        }

        $stmt = $this->db->prepare(
            "SELECT loai_thanh_toan, COALESCE(SUM(so_tien), 0) AS total
             FROM thanh_toan_luong
             WHERE ma_nhan_vien = ? AND ngay_thanh_toan BETWEEN ? AND ?
             GROUP BY loai_thanh_toan"
        );
        $stmt->execute([$employeeId, $from, $to]);
        $result = ['advance' => 0.0, 'salary' => 0.0, 'salary_balance' => 0.0, 'revenue_bonus' => 0.0];
        foreach ($stmt->fetchAll() as $row) {
            $result[$row['loai_thanh_toan']] = (float) $row['total'];
        }

        return $result;
    }

    public function getPayrollSettings(int $employeeId): array
    {
        if (!table_exists('cai_dat_luong')) {
            return ['base_salary' => 0.0];
        }

        $stmt = $this->db->prepare('SELECT luong_co_ban FROM cai_dat_luong WHERE ma_nhan_vien = ?');
        $stmt->execute([$employeeId]);
        $row = $stmt->fetch();

        return ['base_salary' => (float) ($row['luong_co_ban'] ?? 0)];
    }

    public function savePayrollSettings(int $employeeId, float $baseSalary): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO cai_dat_luong (ma_nhan_vien, luong_co_ban) VALUES (?,?)
             ON DUPLICATE KEY UPDATE luong_co_ban = VALUES(luong_co_ban)'
        );
        $stmt->execute([$employeeId, $baseSalary]);
    }

    public function getEmployeeSummary(int $employeeId, string $from, string $to): array
    {
        $periodStart = new \DateTime($from);
        $periodEnd = new \DateTime($to);
        $daysInPeriod = max(1, (int) $periodStart->diff($periodEnd)->days + 1);

        $leaveSummary = $this->getLeaveSummaryForEmployees($from, $to);
        $leave = $leaveSummary[$employeeId] ?? ['authorized_days' => 0, 'unauthorized_days' => 0, 'total_days' => 0];
        $bonusPenalty = $this->sumBonusPenalty($employeeId, $from, $to);
        $payments = $this->sumPayments($employeeId, $from, $to);
        $settings = $this->getPayrollSettings($employeeId);
        $workingDays = $this->countWorkingDays($employeeId, $from, $to);
        $commission = $this->getEmployeeCommission($employeeId, $from, $to);

        $baseSalary = (float) $settings['base_salary'];
        $dailySalary = $daysInPeriod > 0 ? round($baseSalary / $daysInPeriod, 0) : 0.0;

        // Nếu có chấm công: tính theo ngày; nếu không: dùng toàn bộ lương cứng
        if ($workingDays > 0) {
            $earnedBase = round($dailySalary * $workingDays, 0);
        } else {
            $earnedBase = $baseSalary;
        }

        // Trừ lương theo ngày nghỉ:
        // - Nghỉ có phép (authorized):   trừ 20.000 VNĐ / ngày
        // - Nghỉ không phép (unauthorized): trừ 50.000 VNĐ / ngày
        $authorizedDays   = (float) ($leave['authorized_days']   ?? 0);
        $unauthorizedDays = (float) ($leave['unauthorized_days'] ?? 0);
        $leaveDeduction   = round($authorizedDays * 20000 + $unauthorizedDays * 50000, 0);

        $gross = $earnedBase + $commission + $bonusPenalty['bonus'] - $bonusPenalty['penalty'] - $leaveDeduction;
        $paid = $payments['advance'] + $payments['salary'] + $payments['salary_balance'] + $payments['revenue_bonus'];
        $remaining = max(0, $gross - $paid);
        $netSalary = $gross - $payments['advance'];

        return [
            'total_leave_days'    => (float) ($leave['total_days'] ?? 0),
            'authorized_leave'    => $authorizedDays,
            'unauthorized_leave'  => $unauthorizedDays,
            'leave_deduction'     => $leaveDeduction,
            'bonus_total'         => $bonusPenalty['bonus'],
            'penalty_total'       => $bonusPenalty['penalty'],
            'advance_total'       => $payments['advance'],
            'revenue_bonus_total' => $payments['revenue_bonus'],
            'salary_balance'      => $remaining,
            'commission'          => $commission,
            'working_days'        => $workingDays,
            'days_in_period'      => $daysInPeriod,
            'base_salary'         => $baseSalary,
            'daily_salary'        => $dailySalary,
            'net_salary'          => max(0, $netSalary),
            'gross_salary'        => $gross,
        ];
    }

    /** Chi tiết hoa hồng từng dòng dịch vụ trong kỳ */
    public function getCommissionDetails(int $employeeId, string $from, string $to): array
    {
        if (!table_exists('chi_tiet_don_hang') || !table_exists('don_hang')) {
            return [];
        }

        $stmt = $this->db->prepare(
            "SELECT o.ma_don_hang, o.ma_don, o.ngay_tao, oi.ten, oi.tong_dong, oi.loai, oi.ma_nhan_vien, oi.ma_tham_chieu
             FROM chi_tiet_don_hang oi
             INNER JOIN don_hang o ON o.ma_don_hang = oi.ma_don_hang
             WHERE oi.ma_nhan_vien = ?
               AND DATE(o.ngay_tao) BETWEEN ? AND ?
               AND o.trang_thai = 'completed'
             ORDER BY o.ngay_tao DESC"
        );
        $stmt->execute([$employeeId, $from, $to]);
        $rows = $stmt->fetchAll();

        foreach ($rows as &$row) {
            $row['commission'] = $this->commissionForItem($row);
        }

        return $rows;
    }

    public static function paymentTypeLabel(string $type): string
    {
        $map = [
            'advance' => 'Tạm ứng',
            'salary' => 'Trả lương',
            
        ];

        return $map[$type] ?? $type;
    }

    public static function paymentMethodLabel(string $method): string
    {
        $map = [
            'cash' => 'Tiền mặt',
            'transfer' => 'Chuyển khoản',
            'card' => 'Thẻ',
        ];

        return $map[$method] ?? $method;
    }
}
