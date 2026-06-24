<?php

namespace App\Models;

use App\Core\Model;

class Product extends Model
{
    public function getAll(): array
    {
        return $this->db->query('SELECT * FROM san_pham ORDER BY ten_san_pham')->fetchAll();
    }

    public function search(string $q, int $limit = 100): array
    {
        $q = trim($q);
        if ($q === '') {
            return $this->getAll();
        }
        $like = '%' . $q . '%';
        $stmt = $this->db->prepare(
            'SELECT * FROM san_pham WHERE ten_san_pham LIKE ? OR ma_sku LIKE ? ORDER BY ten_san_pham LIMIT ?'
        );
        $stmt->bindValue(1, $like);
        $stmt->bindValue(2, $like);
        $stmt->bindValue(3, $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getLowStock(): array
    {
        return $this->db->query('SELECT * FROM san_pham WHERE so_luong_ton <= so_luong_toi_thieu AND hoat_dong = 1')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM san_pham WHERE ma_san_pham = ?');
        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    public function save(array $data, ?int $id = null): void
    {
        // Hỗ trợ cả key tiếng Việt (từ controller mới) và key tiếng Anh (legacy)
        $tenSanPham   = $data['ten_san_pham']         ?? $data['product_name']  ?? '';
        $maSku        = $data['ma_sku']               ?? $data['sku']           ?? '';
        $donVi        = $data['don_vi']               ?? $data['unit']          ?? 'cai';
        $giaBan       = $data['gia_ban']              ?? $data['sale_price']    ?? 0;
        $giaNhap      = $data['gia_nhap']             ?? $data['cost_price']    ?? 0;
        $soLuongTon   = $data['so_luong_ton']         ?? $data['stock_qty']     ?? 0;
        $soLuongMin   = $data['so_luong_toi_thieu']   ?? $data['min_stock']     ?? 0;
        $hoatDong     = $data['hoat_dong']            ?? $data['is_active']     ?? 1;

        if ($id) {
            $stmt = $this->db->prepare(
                'UPDATE san_pham SET ten_san_pham=?, ma_sku=?, don_vi=?, gia_ban=?, gia_nhap=?, so_luong_ton=?, so_luong_toi_thieu=?, hoat_dong=? WHERE ma_san_pham=?'
            );
            $stmt->execute([$tenSanPham, $maSku, $donVi, $giaBan, $giaNhap, $soLuongTon, $soLuongMin, $hoatDong, $id]);
        } else {
            $stmt = $this->db->prepare(
                'INSERT INTO san_pham (ten_san_pham, ma_sku, don_vi, gia_ban, gia_nhap, so_luong_ton, so_luong_toi_thieu, hoat_dong)
                 VALUES (?,?,?,?,?,?,?,?)'
            );
            $stmt->execute([$tenSanPham, $maSku, $donVi, $giaBan, $giaNhap, $soLuongTon, $soLuongMin, $hoatDong]);
        }
    }

    public function adjustStock(int $id, float $delta): void
    {
        $stmt = $this->db->prepare('UPDATE san_pham SET so_luong_ton = so_luong_ton + ? WHERE ma_san_pham = ?');
        $stmt->execute([$delta, $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM san_pham WHERE ma_san_pham = ?');
        $stmt->execute([$id]);
    }
}
