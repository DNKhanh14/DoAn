<?php

namespace App\Models;

use App\Core\Model;
use Exception;

class Appointment extends Model
{
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
            $clientModel = new Client();
            $clientId = $clientModel->findOrCreate($firstName, $lastName, $phone, $email);

            $stmt = $this->db->prepare(
                'INSERT INTO lich_hen (ngay_tao, ma_khach_hang, ma_nhan_vien, thoi_gian_bat_dau, thoi_gian_ket_thuc_du_kien, trang_thai, nguon_dat)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([date('Y-m-d H:i'), $clientId, $employeeId, $startTime, $endTime, 'pending', 'website']);
            $appointmentId = (int) $this->db->lastInsertId();

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

    public function getCalendarSlots(int $employeeId, array $serviceIds): array
    {
        $serviceModel = new Service();
        $durationMinutes = $serviceModel->getDurationByIds($serviceIds);

        $secs = $durationMinutes * 60;

        // Giờ cố định: T2–T6 09:00–20:00 | T7–CN 09:00–21:00
        $fixedHours = [
            1 => ['open' => '09:00', 'close' => '20:00'], // Thứ 2
            2 => ['open' => '09:00', 'close' => '20:00'], // Thứ 3
            3 => ['open' => '09:00', 'close' => '20:00'], // Thứ 4
            4 => ['open' => '09:00', 'close' => '20:00'], // Thứ 5
            5 => ['open' => '09:00', 'close' => '20:00'], // Thứ 6
            6 => ['open' => '09:00', 'close' => '21:00'], // Thứ 7
            7 => ['open' => '09:00', 'close' => '21:00'], // Chủ nhật
        ];

        $days = [];
        $appointmentDate = date('Y-m-d');

        for ($i = 0; $i < 10; $i++) {
            $appointmentDate = date('Y-m-d', strtotime($appointmentDate . ' +1 day'));

            // dayId: 1=T2 ... 6=T7, 7=CN
            $dayId = (int) date('w', strtotime($appointmentDate));
            if ($dayId === 0) {
                $dayId = 7;
            }

            $hours    = $fixedHours[$dayId];
            $openTime  = $hours['open'];
            $closeTime = $hours['close'];

            $slots = [];
            $start  = $openTime;
            $result = date('H:i', strtotime($start) + $secs);

            while ($start >= $openTime && $result <= $closeTime) {
                // Kiểm tra slot chưa bị đặt
                $stmt = $this->db->prepare(
                    'SELECT *
                     FROM lich_hen a
                     WHERE DATE(thoi_gian_bat_dau) = ?
                       AND a.ma_nhan_vien = ?
                       AND da_huy = 0
                       AND (
                            TIME(thoi_gian_bat_dau) BETWEEN ? AND ?
                            OR TIME(thoi_gian_ket_thuc_du_kien) BETWEEN ? AND ?
                       )'
                );
                $stmt->execute([
                    $appointmentDate,
                    $employeeId,
                    $start,
                    $result,
                    $start,
                    $result,
                ]);

                if ($stmt->rowCount() === 0) {
                    $slots[] = [
                        'value' => $appointmentDate . ' ' . $start . ' ' . $result,
                        'label' => $start,
                        'id'    => $appointmentDate . ' ' . $start,
                    ];
                }

                $start  = date('H:i', strtotime('+15 minutes', strtotime($start)));
                $result = date('H:i', strtotime($start) + $secs);
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

    public function getUpcoming(): array
    {
        $stmt = $this->db->prepare(
            'SELECT *
             FROM lich_hen a, khach_hang c
             WHERE thoi_gian_bat_dau >= ?
               AND a.ma_khach_hang = c.ma_khach_hang
               AND da_huy = 0
             ORDER BY thoi_gian_bat_dau'
        );
        $stmt->execute([date('Y-m-d H:i:s')]);

        return $stmt->fetchAll();
    }

    public function getAllWithClients(): array
    {
        $stmt = $this->db->query(
            'SELECT *
             FROM lich_hen a, khach_hang c
             WHERE a.ma_khach_hang = c.ma_khach_hang
             ORDER BY thoi_gian_bat_dau'
        );

        return $stmt->fetchAll();
    }

    public function getCanceled(): array
    {
        $stmt = $this->db->query(
            'SELECT *
             FROM lich_hen a, khach_hang c
             WHERE da_huy = 1
               AND a.ma_khach_hang = c.ma_khach_hang'
        );

        return $stmt->fetchAll();
    }

    public function getBookedServices(int $appointmentId): array
    {
        $stmt = $this->db->prepare(
            'SELECT ten_dich_vu
             FROM dich_vu s, dich_vu_dat sb
             WHERE s.ma_dich_vu = sb.ma_dich_vu
               AND ma_lich_hen = ?'
        );
        $stmt->execute([$appointmentId]);

        return $stmt->fetchAll();
    }

    public function getEmployeeForAppointment(int $appointmentId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT ten, ho_dem
             FROM nhan_vien e, lich_hen a
             WHERE e.ma_nhan_vien = a.ma_nhan_vien
               AND a.ma_lich_hen = ?'
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
        // check_out = hoàn thành dịch vụ (completed legacy)
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
             INNER JOIN nhan_vien e ON a.ma_nhan_vien = e.ma_nhan_vien
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

    /**
     * @param array<int, array{employee_id: int, service_ids: int[]}> $guestGroups
     * @return int[]
     */
    public function createFromGuestGroups(array $data, array $guestGroups): array
    {
        $serviceModel = new Service();
        $ids = [];

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
            $start = new \DateTime($data['start_time']);
            $end = clone $start;
            $end->modify('+' . $durationMin . ' minutes');

            $ids[] = $this->createManual([
                'ma_khach_hang'             => $data['ma_khach_hang'] ?? $data['client_id'] ?? null,
                'ma_nhan_vien'              => $employeeId,
                'thoi_gian_bat_dau'         => $data['thoi_gian_bat_dau'] ?? $data['start_time'],
                'thoi_gian_ket_thuc_du_kien' => $end->format('Y-m-d H:i:s'),
                'trang_thai'                => $data['trang_thai'] ?? $data['status'] ?? 'confirmed',
                'nguon_dat'                 => $data['nguon_dat'] ?? $data['booking_source'] ?? 'hotline',
            ], $serviceIds);
        }

        return $ids;
    }

    public function createManual(array $data, array $serviceIds): int
    {
        $this->db->beginTransaction();
        try {
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
            $this->db->commit();

            return $id;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function queueReminder(int $appointmentId, string $channel, string $scheduledAt, string $message): void
    {
        // Bảng reminder_queue đã bị xóa, method này không còn cần thiết
    }

    public function getPendingReminders(): array
    {
        return [];
    }

    /** Lấy chi tiết 1 lịch hẹn theo ID */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT a.*, c.ten, c.ho_dem, c.so_dien_thoai,
                    e.ten AS emp_fname, e.ho_dem AS emp_lname
             FROM lich_hen a
             INNER JOIN khach_hang c ON a.ma_khach_hang = c.ma_khach_hang
             INNER JOIN nhan_vien e ON a.ma_nhan_vien = e.ma_nhan_vien
             WHERE a.ma_lich_hen = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /** Cập nhật thời gian lịch hẹn */
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

    /** Cập nhật nhân viên phụ trách */
    public function updateEmployee(int $id, int $employeeId): void
    {
        $stmt = $this->db->prepare(
            'UPDATE lich_hen SET ma_nhan_vien = ? WHERE ma_lich_hen = ?'
        );
        $stmt->execute([$employeeId, $id]);
    }

    /** Đếm lịch hẹn hôm nay (chưa check-out, chưa hủy) — dùng cho badge nav */
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
    }    public function getBookedServicesDetailed(int $appointmentId): array
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
             INNER JOIN nhan_vien e ON a.ma_nhan_vien = e.ma_nhan_vien
             WHERE a.ma_lich_hen = ?'
        );
        $stmt->execute([$appointmentId]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }

        $services = $this->getBookedServicesDetailed($appointmentId);
        $lines = [];
        $empName = strtoupper(trim($row['emp_fname'] . ' ' . $row['emp_lname']));
        foreach ($services as $s) {
            $lines[] = [
                'type' => 'service',
                'ref_id' => (int) $s['ma_dich_vu'],
                'name' => $s['ten_dich_vu'],
                'unit_price' => (float) $s['gia'],
                'qty' => 1,
                'ma_nhan_vien' => (int) $row['ma_nhan_vien'],
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
