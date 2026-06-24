<?php

namespace App\Models;

use App\Core\Model;

class Expense extends Model
{
    public function getAll(): array
    {
        return $this->db->query('SELECT * FROM chi_phi ORDER BY ngay_chi DESC')->fetchAll();
    }

    public function add(string $category, float $amount, string $date, ?string $note): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO chi_phi (danh_muc, so_tien, ngay_chi, ghi_chu) VALUES (?,?,?,?)'
        );
        $stmt->execute([$category, $amount, $date, $note]);
    }

    public function totalBetween(string $from, string $to): float
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(so_tien),0) FROM chi_phi WHERE ngay_chi BETWEEN ? AND ?'
        );
        $stmt->execute([$from, $to]);

        return (float) $stmt->fetchColumn();
    }
}
