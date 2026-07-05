<?php

namespace App\Models;

use App\Core\Model;

class Client extends Model
{
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM khach_hang WHERE email = ?');
        $stmt->execute([$email]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function findByPhone(string $phone): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM khach_hang WHERE so_dien_thoai = ? LIMIT 1');
        $stmt->execute([trim($phone)]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function create(string $firstName, string $lastName, string $phone, string $email): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO khach_hang (ten, ho_dem, so_dien_thoai, email) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$firstName, $lastName, $phone, $email]);

        return (int) $this->db->lastInsertId();
    }

    public function findOrCreate(string $firstName, string $lastName, string $phone, string $email): int
    {
        $existing = $this->findByEmail($email);

        if ($existing) {
            return (int) $existing['ma_khach_hang'];
        }

        return $this->create($firstName, $lastName, $phone, $email);
    }

    public function getAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM khach_hang ORDER BY ma_khach_hang DESC');
        return $stmt->fetchAll();
    }

    public function count(string $q = ''): int
    {
        if ($q !== '') {
            $like = '%' . $q . '%';
            $stmt = $this->db->prepare(
                'SELECT COUNT(*) FROM khach_hang
                 WHERE ten LIKE ? OR ho_dem LIKE ? OR so_dien_thoai LIKE ? OR email LIKE ?'
            );
            $stmt->execute([$like, $like, $like, $like]);
        } else {
            $stmt = $this->db->query('SELECT COUNT(*) FROM khach_hang');
        }
        return (int) $stmt->fetchColumn();
    }

    public function getPaginated(int $offset, int $limit, string $q = ''): array
    {
        if ($q !== '') {
            $like = '%' . $q . '%';
            $stmt = $this->db->prepare(
                'SELECT * FROM khach_hang
                 WHERE ten LIKE ? OR ho_dem LIKE ? OR so_dien_thoai LIKE ? OR email LIKE ?
                 ORDER BY ma_khach_hang DESC LIMIT ? OFFSET ?'
            );
            $stmt->bindValue(1, $like);
            $stmt->bindValue(2, $like);
            $stmt->bindValue(3, $like);
            $stmt->bindValue(4, $like);
            $stmt->bindValue(5, $limit, \PDO::PARAM_INT);
            $stmt->bindValue(6, $offset, \PDO::PARAM_INT);
        } else {
            $stmt = $this->db->prepare(
                'SELECT * FROM khach_hang ORDER BY ma_khach_hang DESC LIMIT ? OFFSET ?'
            );
            $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function search(string $q, int $limit = 20): array
    {
        $q = trim($q);
        if ($q === '') {
            return [];
        }
        $like = '%' . $q . '%';
        $stmt = $this->db->prepare(
            'SELECT * FROM khach_hang
             WHERE ten LIKE ? OR ho_dem LIKE ? OR so_dien_thoai LIKE ? OR email LIKE ?
             ORDER BY ma_khach_hang DESC LIMIT ?'
        );
        $stmt->bindValue(1, $like);
        $stmt->bindValue(2, $like);
        $stmt->bindValue(3, $like);
        $stmt->bindValue(4, $like);
        $stmt->bindValue(5, $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function quickCreate(string $firstName, string $lastName, string $phone, string $email = ''): int
    {
        if ($email === '') {
            $email = 'guest_' . time() . '@salon.local';
        }

        return $this->create($firstName, $lastName, $phone, $email);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM khach_hang WHERE ma_khach_hang = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function updateBasic(int $id, string $firstName, string $lastName, string $phone): void
    {
        $stmt = $this->db->prepare(
            'UPDATE khach_hang SET ten = ?, ho_dem = ?, so_dien_thoai = ? WHERE ma_khach_hang = ?'
        );
        $stmt->execute([$firstName, $lastName, $phone, $id]);
    }

    public function delete(int $id): bool
    {
        try {
            if (table_exists('don_hang')) {
                $this->db->prepare('UPDATE don_hang SET ma_khach_hang = NULL WHERE ma_khach_hang = ?')->execute([$id]);
            }
            $stmt = $this->db->prepare('DELETE FROM khach_hang WHERE ma_khach_hang = ?');
            $stmt->execute([$id]);

            return $stmt->rowCount() > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function updateCrm(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE khach_hang SET ngay_sinh = ?, hang_thanh_vien = ?, diem_tich_luy = ?, so_du_truoc = ? WHERE ma_khach_hang = ?'
        );
        $stmt->execute([
            $data['date_of_birth'] ?: null,
            $data['tier'],
            (int) $data['loyalty_points'],
            (float) $data['prepaid_balance'],
            $id,
        ]);
    }

    public function addLoyaltyPoints(int $id, int $points): void
    {
        $stmt = $this->db->prepare('UPDATE khach_hang SET diem_tich_luy = diem_tich_luy + ? WHERE ma_khach_hang = ?');
        $stmt->execute([$points, $id]);
    }

    public function getBirthdaysThisMonth(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM khach_hang WHERE ngay_sinh IS NOT NULL
             AND MONTH(ngay_sinh) = MONTH(CURDATE())"
        );

        return $stmt->fetchAll();
    }
}
