<?php

namespace App\Models;

use App\Core\Model;

class ServiceCategory extends Model
{
    public function getAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM danh_muc_dich_vu');
        return $stmt->fetchAll();
    }

    public function getAllWithServices(): array
    {
        $categories = $this->getAll();
        $result = [];

        foreach ($categories as $category) {
            $stmt = $this->db->prepare('SELECT * FROM dich_vu WHERE ma_danh_muc = ?');
            $stmt->execute([$category['ma_danh_muc']]);
            $services = $stmt->fetchAll();

            if (count($services) > 0) {
                $category['services'] = $services;
                $result[] = $category;
            }
        }

        return $result;
    }

    public function create(string $name): void
    {
        $stmt = $this->db->prepare('INSERT INTO danh_muc_dich_vu (ten_danh_muc) VALUES (?)');
        $stmt->execute([$name]);
    }

    public function update(int $id, string $name): void
    {
        $stmt = $this->db->prepare('UPDATE danh_muc_dich_vu SET ten_danh_muc = ? WHERE ma_danh_muc = ?');
        $stmt->execute([$name, $id]);
    }

    public function delete(int $id): void
    {
        $this->db->beginTransaction();

        try {
            // Tìm hoặc tạo danh mục "Uncategorized" để chuyển dịch vụ vào
            $stmtUncategorized = $this->db->prepare(
                'SELECT ma_danh_muc FROM danh_muc_dich_vu WHERE LOWER(ten_danh_muc) = ?'
            );
            $stmtUncategorized->execute(['uncategorized']);
            $uncategorized = $stmtUncategorized->fetch();

            if (!$uncategorized) {
                // Tạo mới nếu chưa có
                $this->db->prepare(
                    "INSERT INTO danh_muc_dich_vu (ten_danh_muc) VALUES ('Uncategorized')"
                )->execute();
                $uncategorizedId = (int) $this->db->lastInsertId();
            } else {
                $uncategorizedId = (int) $uncategorized['ma_danh_muc'];
            }

            // Chuyển tất cả dịch vụ thuộc danh mục này sang Uncategorized
            $stmtUpdate = $this->db->prepare(
                'UPDATE dich_vu SET ma_danh_muc = ? WHERE ma_danh_muc = ?'
            );
            $stmtUpdate->execute([$uncategorizedId, $id]);

            // Xóa danh mục
            $stmt = $this->db->prepare('DELETE FROM danh_muc_dich_vu WHERE ma_danh_muc = ?');
            $stmt->execute([$id]);

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getUncategorizedId(): int
    {
        $stmt = $this->db->prepare('SELECT ma_danh_muc FROM danh_muc_dich_vu WHERE LOWER(ten_danh_muc) = ?');
        $stmt->execute(['uncategorized']);
        $row = $stmt->fetch();

        return (int) $row['ma_danh_muc'];
    }
}
