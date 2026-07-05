<?php

namespace App\Models;

use App\Core\Model;

class Service extends Model
{
    public function getAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM dich_vu');
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM dich_vu WHERE ma_dich_vu = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function getDurationByIds(array $serviceIds): int
    {
        $total = 0;

        foreach ($serviceIds as $serviceId) {
            $stmt = $this->db->prepare('SELECT thoi_luong FROM dich_vu WHERE ma_dich_vu = ?');
            $stmt->execute([$serviceId]);
            $row = $stmt->fetch();
            $total += (int) ($row['thoi_luong'] ?? 0);
        }

        return $total;
    }

    public function getAllWithCategories(): array
    {
        $stmt = $this->db->query(
            'SELECT s.*, sc.ten_danh_muc
             FROM dich_vu s
             INNER JOIN danh_muc_dich_vu sc ON s.ma_danh_muc = sc.ma_danh_muc
             ORDER BY sc.ten_danh_muc, s.ten_dich_vu'
        );

        return $stmt->fetchAll();
    }

    /** @return array<string, array<int, array>> */
    public function getGroupedByCategory(): array
    {
        $grouped = [];
        foreach ($this->getAllWithCategories() as $row) {
            $cat = $row['ten_danh_muc'] ?? 'Khác';
            $grouped[$cat][] = $row;
        }

        return $grouped;
    }

    public function searchWithCategories(string $q, int $limit = 100): array
    {
        $q = trim($q);
        if ($q === '') {
            return $this->getAllWithCategories();
        }
        $like = '%' . $q . '%';
        $stmt = $this->db->prepare(
            'SELECT s.*, sc.ten_danh_muc
             FROM dich_vu s
             INNER JOIN danh_muc_dich_vu sc ON s.ma_danh_muc = sc.ma_danh_muc
             WHERE s.ten_dich_vu LIKE ? OR s.mo_ta LIKE ? OR sc.ten_danh_muc LIKE ?
             ORDER BY sc.ten_danh_muc, s.ten_dich_vu
             LIMIT ?'
        );
        $stmt->bindValue(1, $like);
        $stmt->bindValue(2, $like);
        $stmt->bindValue(3, $like);
        $stmt->bindValue(4, $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function create(array $data): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO dich_vu (ten_dich_vu, mo_ta, gia, thoi_luong, ma_danh_muc)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['ten_dich_vu'] ?? $data['service_name'] ?? '',
            $data['mo_ta'] ?? $data['service_description'] ?? '',
            $data['gia'] ?? $data['service_price'] ?? 0,
            $data['thoi_luong'] ?? $data['service_duration'] ?? 0,
            $data['ma_danh_muc'] ?? $data['category_id'] ?? 0,
        ]);
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE dich_vu SET ten_dich_vu = ?, mo_ta = ?, gia = ?,
             thoi_luong = ?, ma_danh_muc = ? WHERE ma_dich_vu = ?'
        );
        $stmt->execute([
            $data['ten_dich_vu'] ?? $data['service_name'] ?? '',
            $data['mo_ta'] ?? $data['service_description'] ?? '',
            $data['gia'] ?? $data['service_price'] ?? 0,
            $data['thoi_luong'] ?? $data['service_duration'] ?? 0,
            $data['ma_danh_muc'] ?? $data['category_id'] ?? 0,
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM dich_vu WHERE ma_dich_vu = ?');
        $stmt->execute([$id]);
    }

    public function getByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db->prepare("SELECT * FROM dich_vu WHERE ma_dich_vu IN ($placeholders)");
        $stmt->execute(array_values($ids));

        return $stmt->fetchAll();
    }
}
