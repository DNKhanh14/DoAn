<?php

namespace App\Models;

use App\Core\Model;
use Exception;
use DateTime;

class Appointment extends Model
{
    /** Giờ mở cửa: T2–T6 09:00–20:00 | T7–CN 09:00–21:00 */
    private const SHOP_HOURS = [
        1 => ['open' => '09:00', 'close' => '20:00'],
        2 => ['open' => '09:00', 'close' => '20:00'],
        3 => ['open' => '09:00', 'close' => '20:00'],
        4 => ['open' => '09:00', 'close' => '20:00'],
        5 => ['open' => '09:00', 'close' => '20:00'],
        6 => ['open' => '09:00', 'close' => '21:00'],
        7 => ['open' => '09:00', 'close' => '21:00'],
    ];

    private const CALENDAR_DAYS = 10;
    private const SLOT_STEP_MINUTES = 15;

    /**
     * Kiểm tra thợ đã có lịch trùng khung giờ chưa
     */
    public function hasScheduleConflict(
        int $employeeId,
        string $startTime,
        string $endTime,
        ?int $excludeAppointmentId = null,
        bool $lock = false
    ): bool {
        $sql = 'SELECT COUNT(*) FROM lich_hen
                WHERE ma_nhan_vien = ?
                  AND da_huy = 0
                  AND thoi_gian_bat_dau < ?
                  AND thoi_gian_ket_thuc_du_kien > ?';
        $params = [$employeeId, $endTime, $startTime];

        if ($excludeAppointmentId !== null && $excludeAppointmentId > 0) {
            $sql .= ' AND ma_lich_hen <> ?';
            $params[] = $excludeAppointmentId;
        }

        // Nếu đang true thì sẽ khóa không cho truy vấn bảng lịch hẹn tránh 2 ng chọn cùng 1 ng 1 time 
        if ($lock) {
            $sql .= ' FOR UPDATE';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    private function getBookedRangesForEmployee(int $employeeId, string $fromDate, string $toDate): array
    {
        $stmt = $this->db->prepare(
            'SELECT thoi_gian_bat_dau, thoi_gian_ket_thuc_du_kien
             FROM lich_hen
             WHERE ma_nhan_vien = ?
               AND da_huy = 0
               AND DATE(thoi_gian_bat_dau) BETWEEN ? AND ?
             ORDER BY thoi_gian_bat_dau'
        );
        $stmt->execute([$employeeId, $fromDate, $toDate]);

        $ranges = [];
        foreach ($stmt->fetchAll() as $row) {
            $ranges[] = [
                'start' => $row['thoi_gian_bat_dau'],
                'end'   => $row['thoi_gian_ket_thuc_du_kien'],
            ];
        }

        return $ranges;
    }
//kiểm tra time bận của thợ 
    private function rangesOverlap(string $startA, string $endA, string $startB, string $endB): bool
    {
        return strtotime($startA) < strtotime($endB) && strtotime($endA) > strtotime($startB);
    }

    private function weekdayId(string $date): int
    {
        $dayId = (int) date('w', strtotime($date));
        return $dayId === 0 ? 7 : $dayId;
    }

    /**
     * Đặt lịch trực tuyến từ Website
     */
    public function book(
        array $serviceIds,
        int $employeeId,
        string $startTime,
        string $endTime,
        string $firstName,
        string $lastName,
        string $phone,
        string $email
    ): void {
        $this->db->beginTransaction();

        try {
            // Bật cờ true để LOCK dòng chống tình trạng trùng lịch khi 2 người bấm cùng giây
            if ($this->hasScheduleConflict($employeeId, $startTime, $endTime, null, true)) {
                throw new Exception('Khung giờ này đã được đặt trước. Vui lòng chọn giờ khác.');
            }

            $clientModel = new Client();
            $clientId = $clientModel->findOrCreate($firstName, $lastName, $phone, $email);

            $stmt = $this->db->prepare(
                'INSERT INTO lich_hen (ngay_tao, ma_khach_hang, ma_nhan_vien, thoi_gian_bat_dau, thoi_gian_ket_thuc_du_kien, trang_thai, nguon_dat)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([date('Y-m-d H:i'), $clientId, $employeeId, $startTime, $endTime, 'confirmed', 'website']);
            $appointmentId = (int) $this->db->lastInsertId();

            // Chuẩn bị câu lệnh một lần trước vòng lặp
            $bookStmt = $this->db->prepare(
                'INSERT INTO dich_vu_dat (ma_lich_hen, ma_dich_vu) VALUES (?, ?)'
            );

            foreach ($serviceIds as $serviceId) {
                $bookStmt->execute([$appointmentId, $serviceId]);
            }

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Lấy khung giờ trống trong 10 ngày tới của 1 thợ
     */
    public function getCalendarSlots(int $employeeId, array $serviceIds): array
    {
        $durationMinutes = (new Service())->getDurationByIds($serviceIds);
        if ($durationMinutes < 15) {
            $durationMinutes = 15;
        }
        $durationSecs = $durationMinutes * 60;

        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+' . (self::CALENDAR_DAYS - 1) . ' days', strtotime($startDate)));
        $bookedRanges = $this->getBookedRangesForEmployee($employeeId, $startDate, $endDate); //thu thập khoảng thời gian bận của barber

        $now = time();
        $days = [];

        for ($i = 0; $i < self::CALENDAR_DAYS; $i++) {
            $appointmentDate = date('Y-m-d', strtotime("+{$i} days", strtotime($startDate)));
            $hours = self::SHOP_HOURS[$this->weekdayId($appointmentDate)];
            $openTime = $hours['open'];
            $closeTime = $hours['close'];

            $slots = [];
            $start = $openTime;
            $endLabel = date('H:i', strtotime($start) + $durationSecs);

            while ($start >= $openTime && $endLabel <= $closeTime) {
                $slotStart = $appointmentDate . ' ' . $start;
                $slotEnd = $appointmentDate . ' ' . $endLabel;

                if (strtotime($slotStart) >= $now) {
                    $available = true;
                    foreach ($bookedRanges as $range) {
                        if ($this->rangesOverlap($slotStart, $slotEnd, $range['start'], $range['end'])) {
                            $available = false;
                            break;
                        }
                    }
                    if ($available) {
                        $slots[] = [
                            'value' => $appointmentDate . ' ' . $start . ' ' . $endLabel,
                            'label' => $start,
                            'id'    => $appointmentDate . ' ' . $start,
                        ];
                    }
                }

                $start = date('H:i', strtotime('+' . self::SLOT_STEP_MINUTES . ' minutes', strtotime($start)));
                $endLabel = date('H:i', strtotime($start) + $durationSecs);
            }

            $days[] = [
                'date'       => $appointmentDate,
                'day_label'  => date('D', strtotime($appointmentDate)),
                'date_label' => date('d', strtotime($appointmentDate)) . ' ' . date('M', strtotime($appointmentDate)),
                'slots'      => $slots,
            ];
        }

        return $days;
    }

    /**
     * INNER JOIN cho đồng bộ sạch sẽ
     */
    public function getUpcoming(): array
    {
        $stmt = $this->db->prepare(
            'SELECT a.*, c.ten, c.ho_dem, c.so_dien_thoai, c.email
             FROM lich_hen a
             INNER JOIN khach_hang c ON a.ma_khach_hang = c.ma_khach_hang
             WHERE a.thoi_gian_bat_dau >= ?
               AND a.da_huy = 0
             ORDER BY a.thoi_gian_bat_dau'
        );
        $stmt->execute([date('Y-m-d H:i:s')]);

        return $stmt->fetchAll();
    }

  
    public function getAllWithClients(): array
    {
        $stmt = $this->db->query(
            'SELECT a.*, c.ten, c.ho_dem, c.so_dien_thoai, c.email
             FROM lich_hen a
             INNER JOIN khach_hang c ON a.ma_khach_hang = c.ma_khach_hang
             ORDER BY a.thoi_gian_bat_dau'
        );

        return $stmt->fetchAll();
    }

   
    public function getCanceled(): array
    {
        $stmt = $this->db->query(
            'SELECT a.*, c.ten, c.ho_dem, c.so_dien_thoai, c.email
             FROM lich_hen a
             INNER JOIN khach_hang c ON a.ma_khach_hang = c.ma_khach_hang
             WHERE a.da_huy = 1'
        );

        return $stmt->fetchAll();
    }

    
    public function getBookedServices(int $appointmentId): array
    {
        $stmt = $this->db->prepare(
            'SELECT s.ten_dich_vu
             FROM dich_vu s
             INNER JOIN dich_vu_dat sb ON s.ma_dich_vu = sb.ma_dich_vu
             WHERE sb.ma_lich_hen = ?'
        );
        $stmt->execute([$appointmentId]);

        return $stmt->fetchAll();
    }

    /**
     * SỬA LỖI: Đổi sang INNER JOIN
     */
    public function getEmployeeForAppointment(int $appointmentId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT e.ten, e.ho_dem
             FROM nhan_vien e
             INNER JOIN lich_hen a ON e.ma_nhan_vien = a.ma_nhan_vien
             WHERE a.ma_lich_hen = ?'
        );
        $stmt->execute([$appointmentId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function cancel(int $appointmentId, string $reason): void
    {
       
        if (!salon_upgrade_required()) {
            $stmt = $this->db->prepare(
                'UPDATE lich_hen SET da_huy = 1, trang_thai = ?, ly_do_huy = ? WHERE ma_lich_hen = ?'
            );
            $stmt->execute(['cancelled', $reason, $appointmentId]);
        } else {
            $stmt = $this->db->prepare(
                'UPDATE lich_hen SET da_huy = 1, ly_do_huy = ? WHERE ma_lich_hen = ?'
            );
            $stmt->execute([$reason, $appointmentId]);
        }
    }

    public function updateStatus(int $id, string $status, ?string $adminNote = null): void
    {
        $canceled = ($status === 'cancelled') ? 1 : 0;
        $stmt = $this->db->prepare(
            'UPDATE lich_hen SET trang_thai = ?, da_huy = ? WHERE ma_lich_hen = ?'
        );
        $stmt->execute([$status, $canceled, $id]);
    }

    public function getByDateRange(string $from, string $to): array
    {
        $stmt = $this->db->prepare(
            "SELECT a.*, c.ten, c.ho_dem, c.so_dien_thoai, c.email,
                    e.ten AS emp_fname, e.ho_dem AS emp_lname
             FROM lich_hen a
             INNER JOIN khach_hang c ON a.ma_khach_hang = c.ma_khach_hang
             LEFT JOIN nhan_vien e ON a.ma_nhan_vien = e.ma_nhan_vien
             WHERE DATE(a.thoi_gian_bat_dau) BETWEEN ? AND ?
               AND a.da_huy = 0
               AND LOWER(a.trang_thai) NOT IN ('cancelled')
             ORDER BY a.thoi_gian_bat_dau ASC"
        );
        $stmt->execute([$from, $to]);

        return $stmt->fetchAll();
    }

    public function hasPaidOrder(int $appointmentId): bool
    {
        if (!table_exists('don_hang')) {
            return false;
        }

        $stmt = $this->db->prepare(
            "SELECT 1 FROM don_hang WHERE ma_lich_hen = ? AND trang_thai = 'completed' LIMIT 1"
        );
        $stmt->execute([$appointmentId]);

        return (bool) $stmt->fetchColumn();
    }

    public function createFromGuestGroups(array $data, array $guestGroups): array
    {
        $serviceModel = new Service();
        $ids = [];

        // Khởi động giao dịch lớn cho cả group ngay từ ngoài vòng lặp
        $this->db->beginTransaction();

        try {
            foreach ($guestGroups as $group) {
                $serviceIds = array_values(array_unique(array_filter($group['service_ids'] ?? [])));
                $employeeId = (int) ($group['ma_nhan_vien'] ?? 0);
                if ($serviceIds === [] || $employeeId <= 0) {
                    continue;
                }

                $durationMin = $serviceModel->getDurationByIds($serviceIds);
                if ($durationMin < 15) {
                    $durationMin = 30;
                }
                $start = new DateTime($data['start_time'] ?? $data['thoi_gian_bat_dau']);
                $end = clone $start;
                $end->modify('+' . $durationMin . ' minutes');

                // Gọi trực tiếp logic tạo (được chia nhỏ bên dưới) không lặp lướt Transaction con nữa
                $ids[] = $this->insertManualWithLock([
                    'ma_khach_hang'             => $data['ma_khach_hang'] ?? $data['client_id'] ?? null,
                    'ma_nhan_vien'              => $employeeId,
                    'thoi_gian_bat_dau'         => $data['thoi_gian_bat_dau'] ?? $data['start_time'],
                    'thoi_gian_ket_thuc_du_kien' => $end->format('Y-m-d H:i:s'),
                    'trang_thai'                => $data['trang_thai'] ?? $data['status'] ?? 'confirmed',
                    'nguon_dat'                 => $data['nguon_dat'] ?? $data['booking_source'] ?? 'hotline',
                ], $serviceIds);
            }

            $this->db->commit();
            return $ids;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function createManual(array $data, array $serviceIds): int
    {
        $this->db->beginTransaction();
        try {
            $id = $this->insertManualWithLock($data, $serviceIds);
            $this->db->commit();
            return $id;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * HÀM BỔ TRỢ: Tách biệt logic Insert để tái sử dụng cho cả đặt đơn lẫn đặt nhóm mà không bị lỗi lặp Transaction
     */
    private function insertManualWithLock(array $data, array $serviceIds): int
    {
        if ($this->hasScheduleConflict(
            (int) $data['ma_nhan_vien'],
            $data['thoi_gian_bat_dau'],
            $data['thoi_gian_ket_thuc_du_kien'],
            null,
            true // Bật lock chống trùng lịch
        )) {
            throw new Exception('Thợ đã có lịch trùng khung giờ này. Vui lòng chọn giờ hoặc thợ khác.');
        }

        $stmt = $this->db->prepare(
            'INSERT INTO lich_hen (ngay_tao, ma_khach_hang, ma_nhan_vien, thoi_gian_bat_dau, thoi_gian_ket_thuc_du_kien, trang_thai, nguon_dat)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            date('Y-m-d H:i'),
            $data['ma_khach_hang'],
            $data['ma_nhan_vien'],
            $data['thoi_gian_bat_dau'],
            $data['thoi_gian_ket_thuc_du_kien'],
            $data['trang_thai'] ?? 'confirmed',
            $data['nguon_dat'] ?? 'hotline',
        ]);
        
        $id = (int) $this->db->lastInsertId();
        
        $book = $this->db->prepare('INSERT INTO dich_vu_dat (ma_lich_hen, ma_dich_vu) VALUES (?, ?)');
        foreach ($serviceIds as $sid) {
            $book->execute([$id, $sid]);
        }

        return $id;
    }

    // TỐI ƯU: Đã dọn dẹp các hàm rác reminder không sử dụng

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT a.*, c.ten, c.ho_dem, c.so_dien_thoai,
                    e.ten AS emp_fname, e.ho_dem AS emp_lname
             FROM lich_hen a
             INNER JOIN khach_hang c ON a.ma_khach_hang = c.ma_khach_hang
             LEFT JOIN nhan_vien e ON a.ma_nhan_vien = e.ma_nhan_vien
             WHERE a.ma_lich_hen = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function updateTime(int $id, string $startTime, string $endTime = ''): void
    {
        if ($endTime === '') {
            $stmt = $this->db->prepare(
                'UPDATE lich_hen SET thoi_gian_bat_dau = ? WHERE ma_lich_hen = ?'
            );
            $stmt->execute([$startTime, $id]);
        } else {
            $stmt = $this->db->prepare(
                'UPDATE lich_hen SET thoi_gian_bat_dau = ?, thoi_gian_ket_thuc_du_kien = ? WHERE ma_lich_hen = ?'
            );
            $stmt->execute([$startTime, $endTime, $id]);
        }
    }

    public function updateEmployee(int $id, int $employeeId): void
    {
        $stmt = $this->db->prepare(
            'UPDATE lich_hen SET ma_nhan_vien = ? WHERE ma_lich_hen = ?'
        );
        $stmt->execute([$employeeId, $id]);
    }

    public function countToday(): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM lich_hen
             WHERE DATE(thoi_gian_bat_dau) = CURDATE()
               AND da_huy = 0
               AND LOWER(trang_thai) NOT IN ('check_out','checkout','completed','cancelled')"
        );
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function getBookedServicesDetailed(int $appointmentId): array
    {
        $stmt = $this->db->prepare(
            'SELECT s.ma_dich_vu, s.ten_dich_vu, s.gia, s.thoi_luong
             FROM dich_vu_dat sb
             INNER JOIN dich_vu s ON sb.ma_dich_vu = s.ma_dich_vu
             WHERE sb.ma_lich_hen = ?
             ORDER BY s.ten_dich_vu'
        );
        $stmt->execute([$appointmentId]);

        return $stmt->fetchAll();
    }

    public function getPosPrefill(int $appointmentId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT a.*, c.ma_khach_hang, c.ten, c.ho_dem, c.so_dien_thoai,
                    e.ma_nhan_vien, e.ten AS emp_fname, e.ho_dem AS emp_lname
             FROM lich_hen a
             INNER JOIN khach_hang c ON a.ma_khach_hang = c.ma_khach_hang
             LEFT JOIN nhan_vien e ON a.ma_nhan_vien = e.ma_nhan_vien
             WHERE a.ma_lich_hen = ?'
        );
        $stmt->execute([$appointmentId]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }

        $services = $this->getBookedServicesDetailed($appointmentId);
        $lines = [];
        $empName = mb_strtoupper(trim($row['emp_fname'] . ' ' . $row['emp_lname']), 'UTF-8');
        foreach ($services as $s) {
            $lines[] = [
                'type' => 'service',
                'ref_id' => (int) $s['ma_dich_vu'],
                'name' => $s['ten_dich_vu'],
                'unit_price' => (float) $s['gia'],
                'qty' => 1,
                'employee_id' => (int) $row['ma_nhan_vien'],
                'employee_name' => $empName,
            ];
        }

        return [
            'appointment_id' => $appointmentId,
            'client_id' => (int) $row['ma_khach_hang'],
            'client_name' => trim($row['ten'] . ' ' . $row['ho_dem']),
            'ma_nhan_vien' => (int) $row['ma_nhan_vien'],
            'lines' => $lines,
        ];
    }
}