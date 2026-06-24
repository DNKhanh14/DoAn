<?php

/** Nhãn trạng thái lịch hẹn (tiếng Việt) */
function appointment_status_label(string $status): string
{
    $map = [
        'confirmed'  => 'Đã xác nhận',
        'check_in'   => 'Check-in',
        'check_out'  => 'Check-out',
        // Legacy mapping
        'pending'    => 'Đã xác nhận',
        'arrived'    => 'Check-in',
        'in_service' => 'Check-in',
        'completed'  => 'Check-out',
        'cancelled'  => 'Đã hủy',
    ];

    return $map[$status] ?? $status;
}

function appointment_status_class(string $status): string
{
    $map = [
        'confirmed'  => 'warning',
        'check_in'   => 'primary',
        'check_out'  => 'success',
        'pending'    => 'warning',
        'arrived'    => 'primary',
        'in_service' => 'primary',
        'completed'  => 'success',
        'cancelled'  => 'danger',
    ];

    return $map[$status] ?? 'secondary';
}

function booking_source_label(string $source): string
{
    $map = [
        'website' => 'Website',
        'fanpage' => 'Fanpage',
        'hotline' => 'Hotline',
        'walk_in' => 'Khách vãng lai',
    ];

    return $map[$source] ?? $source;
}

function client_tier_label(string $tier): string
{
    $map = [
        'member' => 'Thành viên',
        'silver' => 'Bạc',
        'gold' => 'Vàng',
        'diamond' => 'Kim cương',
    ];

    return $map[$tier] ?? $tier;
}

/**
 * Hiển thị số tiền VND. VD: 100000 → "100.000 VND"
 */
function format_vnd($amount): string
{
    return number_format((float) $amount, 0, ',', '.') . ' VND';
}

/**
 * Đọc số tiền từ form (tránh lỗi "300.000" bị PHP hiểu thành 300).
 * Bỏ dấu chấm/ngăn cách nghìn, chỉ giữ phần nguyên VND.
 */
function parse_vnd_input($value): float
{
    if ($value === null || $value === '') {
        return 0.0;
    }
    $s = trim((string) $value);
    $s = str_replace([' ', "\xc2\xa0"], '', $s);

    // Bỏ tất cả dấu chấm (phân cách nghìn kiểu VN: 2.700.000)
    // rồi chỉ giữ chữ số
    $s = str_replace('.', '', $s);
    $s = str_replace(',', '', $s);
    $s = preg_replace('/[^0-9\-]/', '', $s);

    if ($s === '' || $s === '-') {
        return 0.0;
    }

    return (float) $s;
}

/** Alias tương thích code cũ */
function format_money($amount): string
{
    return format_vnd($amount);
}

/** Số lượng / tồn kho — không gắn VND */
function format_number($amount): string
{
    return number_format((float) $amount, 0, ',', '.');
}

/** Mã khách hiển thị kiểu EasySalon: TH000002, KH000015... */
function client_display_code(array $client): string
{
    $first = trim($client['ten'] ?? '');
    $last = trim($client['ho_dem'] ?? '');
    $full = trim($first . ' ' . $last);
    $parts = array_values(array_filter(preg_split('/\s+/u', $full)));

    $toAsciiLetters = static function (string $word): string {
        $word = mb_strtolower($word, 'UTF-8');
        $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $word);
        if ($converted !== false) {
            $word = $converted;
        }

        return preg_replace('/[^a-z]/', '', strtolower($word));
    };

    if (count($parts) >= 2) {
        $letters = $toAsciiLetters($parts[count($parts) - 1]);
        $prefix = strtoupper(substr($letters, 0, 2));
    } else {
        $letters = $toAsciiLetters($parts[0] ?? 'kh');
        $prefix = strtoupper(substr($letters, 0, 2));
    }

    if (strlen($prefix) < 2) {
        $prefix = str_pad($prefix, 2, 'K');
    }

    return $prefix . str_pad((string) (int) ($client['ma_khach_hang'] ?? 0), 6, '0', STR_PAD_LEFT);
}

function appointment_display_code(int $appointmentId): string
{
    return 'LH' . str_pad((string) $appointmentId, 6, '0', STR_PAD_LEFT);
}

function table_exists(string $table): bool
{
    try {
        $db = \App\Core\Database::getConnection();
        $stmt = $db->prepare('SHOW TABLES LIKE ?');
        $stmt->execute([$table]);

        return $stmt->rowCount() > 0;
    } catch (\Throwable $e) {
        return false;
    }
}

function salon_upgrade_required(): bool
{
    return !table_exists('san_pham') || !table_exists('don_hang');
}

function hr_payroll_upgrade_required(): bool
{
    return !table_exists('nghi_phep') || !table_exists('thanh_toan_luong');
}

function payment_method_label(string $method): string
{
    $map = [
        'cash' => 'Tiền mặt',
        'transfer' => 'Chuyển khoản',
        'card' => 'Thẻ',
        'prepaid' => 'Thẻ trả trước',
        'bank' => 'Chuyển khoản',
    ];

    return $map[$method] ?? $method;
}
