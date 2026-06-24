<?php

namespace App\Models;

use App\Core\Model;

class CommissionRate extends Model
{
    /** @return array<string, array<int, float>> */
    public function getAllMapped(?int $employeeId = null): array
    {
        if (!table_exists('chi_tiet_hoa_hong')) {
            return ['dich_vu' => [], 'san_pham' => []];
        }

        if ($employeeId === null || $employeeId <= 0) {
            return ['dich_vu' => [], 'san_pham' => []];
        }

        $stmt = $this->db->prepare('SELECT * FROM chi_tiet_hoa_hong WHERE ma_nhan_vien = ?');
        $stmt->execute([$employeeId]);

        $map = ['dich_vu' => [], 'san_pham' => []];
        foreach ($stmt->fetchAll() as $row) {
            $type = $row['loai_doi_tuong'];
            $id = (int) $row['ma_doi_tuong'];
            $map[$type][$id] = (float) $row['phan_tram_hoa_hong'];
        }

        return $map;
    }

    public function saveItem(string $entityType, int $entityId, ?float $commission, ?int $employeeId = null): void
    {
        if (!table_exists('chi_tiet_hoa_hong') || $employeeId === null || $employeeId <= 0) {
            return;
        }

        // Chuyển đổi entity type sang tiếng Việt
        $typeMap = ['service' => 'dich_vu', 'product' => 'san_pham'];
        $vietnameseType = $typeMap[$entityType] ?? $entityType;

        $stmt = $this->db->prepare(
            'INSERT INTO chi_tiet_hoa_hong (loai_doi_tuong, ma_doi_tuong, ma_nhan_vien, phan_tram_hoa_hong)
             VALUES (?,?,?,?)
             ON DUPLICATE KEY UPDATE phan_tram_hoa_hong=VALUES(phan_tram_hoa_hong)'
        );
        $stmt->execute([$vietnameseType, $entityId, $employeeId, $commission ?? 0]);
    }

    /**
     * % hoa hồng cho dòng hóa đơn — ưu tiên: dịch vụ > mặc định.
     */
    public function resolvePercent(array $item, ?int $employeeId = null): float
    {
        if ($employeeId === null || $employeeId <= 0) {
            return 0.0;
        }

        $map = $this->getAllMapped($employeeId);
        $type = $item['item_type'] ?? '';
        $refId = (int) ($item['ref_id'] ?? 0);

        // Chuyển đổi type sang tiếng Việt
        $typeMap = ['service' => 'dich_vu', 'product' => 'san_pham'];
        $vietnameseType = $typeMap[$type] ?? $type;

        return $map[$vietnameseType][$refId] ?? 0.0;
    }
}
