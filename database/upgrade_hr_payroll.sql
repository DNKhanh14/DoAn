-- ============================================================
-- NÂNG CẤP HR PAYROLL
-- Chạy file này trong phpMyAdmin để bật đầy đủ tính năng lương
-- ============================================================
USE `barbershop`;

-- Bảng ngày nghỉ phép
CREATE TABLE IF NOT EXISTS `nghi_phep` (
  `ma` int(11) NOT NULL AUTO_INCREMENT,
  `ma_nhan_vien` int(11) NOT NULL,
  `tu_ngay` date NOT NULL,
  `den_ngay` date NOT NULL,
  `so_ngay_nghi` decimal(4,1) NOT NULL DEFAULT 1.0,
  `trang_thai` enum('co_phep','khong_phep') NOT NULL DEFAULT 'co_phep',
  `ghi_chu` varchar(255) DEFAULT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ma`),
  KEY `idx_nhan_vien` (`ma_nhan_vien`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng thưởng/phạt
CREATE TABLE IF NOT EXISTS `thuong_phat` (
  `ma` int(11) NOT NULL AUTO_INCREMENT,
  `ma_nhan_vien` int(11) NOT NULL,
  `ngay_ghi` date NOT NULL,
  `loai_ghi` enum('bonus','penalty') NOT NULL DEFAULT 'bonus',
  `danh_muc` varchar(100) NOT NULL DEFAULT '',
  `so_tien` decimal(12,2) NOT NULL DEFAULT 0.00,
  `ghi_chu` varchar(255) DEFAULT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ma`),
  KEY `idx_nhan_vien` (`ma_nhan_vien`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng thanh toán lương
CREATE TABLE IF NOT EXISTS `thanh_toan_luong` (
  `ma_thanh_toan` int(11) NOT NULL AUTO_INCREMENT,
  `ma_nhan_vien` int(11) NOT NULL,
  `loai_thanh_toan` varchar(30) NOT NULL DEFAULT 'salary',
  `phuong_thuc` varchar(20) NOT NULL DEFAULT 'cash',
  `so_tien` decimal(12,2) NOT NULL DEFAULT 0.00,
  `ghi_chu` varchar(255) DEFAULT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ma_thanh_toan`),
  KEY `idx_nhan_vien` (`ma_nhan_vien`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng cài đặt lương
CREATE TABLE IF NOT EXISTS `cai_dat_luong` (
  `ma_nhan_vien` int(11) NOT NULL,
  `luong_co_ban` decimal(12,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`ma_nhan_vien`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- HOÀN TẤT — Reload trang Quản lý lương để dùng đầy đủ tính năng
-- ============================================================


-- ============================================================
-- CẬP NHẬT CẤU TRÚC bảng nghi_phep (nếu đã tồn tại từ phiên bản cũ)
-- Chạy các lệnh này để đồng bộ cột trang_thai mới
-- ============================================================

-- Thêm cột trang_thai nếu chưa có
ALTER TABLE `nghi_phep`
  ADD COLUMN IF NOT EXISTS `trang_thai` enum('co_phep','khong_phep') NOT NULL DEFAULT 'co_phep';

-- Đồng bộ dữ liệu cũ từ cột khong_phep_duyet sang trang_thai
UPDATE `nghi_phep`
SET `trang_thai` = CASE WHEN `khong_phep_duyet` = 1 THEN 'khong_phep' ELSE 'co_phep' END
WHERE `trang_thai` IS NULL OR `trang_thai` = 'co_phep';

-- Xóa các cột cũ không dùng nữa (tuỳ chọn, chỉ chạy nếu muốn dọn dẹp)
-- ALTER TABLE `nghi_phep` DROP COLUMN IF EXISTS `nua_ngay`;
-- ALTER TABLE `nghi_phep` DROP COLUMN IF EXISTS `khong_tra_luong`;
-- ALTER TABLE `nghi_phep` DROP COLUMN IF EXISTS `khong_phep_duyet`;

-- ============================================================
-- HOÀN TẤT — Reload trang Quản lý lương để dùng đầy đủ tính năng
-- ============================================================
