-- ============================================================
-- DATABASE BARBERSHOP - TIẾNG VIỆT
-- File này bao gồm: cấu trúc bảng tiếng Việt + data mẫu
-- Chạy file này để tạo database hoàn chỉnh từ đầu
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

DROP DATABASE IF EXISTS `barbershop`;
CREATE DATABASE `barbershop` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `barbershop`;

-- ============================================================
-- 1. BẢNG CƠ BẢN
-- ============================================================

-- Bảng danh mục dịch vụ
CREATE TABLE `danh_muc_dich_vu` (
  `ma_danh_muc` int(2) NOT NULL AUTO_INCREMENT,
  `ten_danh_muc` varchar(50) NOT NULL,
  PRIMARY KEY (`ma_danh_muc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng quản trị viên
CREATE TABLE `quan_tri_vien` (
  `ma_quan_tri` int(5) NOT NULL AUTO_INCREMENT,
  `ten_dang_nhap` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `ho_ten` varchar(50) NOT NULL,
  `mat_khau` varchar(255) NOT NULL,
  PRIMARY KEY (`ma_quan_tri`),
  UNIQUE KEY `uk_username_email` (`ten_dang_nhap`,`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng khách hàng
CREATE TABLE `khach_hang` (
  `ma_khach_hang` int(5) NOT NULL AUTO_INCREMENT,
  `ten` varchar(30) NOT NULL,
  `ho_dem` varchar(30) NOT NULL,
  `so_dien_thoai` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `ngay_sinh` date DEFAULT NULL,
  `hang_thanh_vien` varchar(20) NOT NULL DEFAULT 'member',
  `diem_tich_luy` int(11) NOT NULL DEFAULT 0,
  `so_du_truoc` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ma_khach_hang`),
  UNIQUE KEY `uk_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng nhân viên
CREATE TABLE `nhan_vien` (
  `ma_nhan_vien` int(2) NOT NULL AUTO_INCREMENT,
  `ten` varchar(20) NOT NULL,
  `ho_dem` varchar(20) NOT NULL,
  `so_dien_thoai` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `chuc_vu` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`ma_nhan_vien`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng dịch vụ
CREATE TABLE `dich_vu` (
  `ma_dich_vu` int(5) NOT NULL AUTO_INCREMENT,
  `ten_dich_vu` varchar(50) NOT NULL,
  `mo_ta` varchar(255) NOT NULL,
  `gia` int(11) NOT NULL,
  `thoi_luong` int(5) NOT NULL,
  `ma_danh_muc` int(2) NOT NULL,
  PRIMARY KEY (`ma_dich_vu`),
  KEY `fk_danh_muc_dich_vu` (`ma_danh_muc`),
  CONSTRAINT `fk_danh_muc_dich_vu` FOREIGN KEY (`ma_danh_muc`) REFERENCES `danh_muc_dich_vu` (`ma_danh_muc`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng lịch hẹn
CREATE TABLE `lich_hen` (
  `ma_lich_hen` int(5) NOT NULL AUTO_INCREMENT,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  `ma_khach_hang` int(5) NOT NULL,
  `ma_nhan_vien` int(2) NOT NULL,
  `thoi_gian_bat_dau` timestamp NOT NULL DEFAULT current_timestamp(),
  `thoi_gian_ket_thuc_du_kien` timestamp NOT NULL DEFAULT current_timestamp(),
  `da_huy` tinyint(1) NOT NULL DEFAULT 0,
  `ly_do_huy` text DEFAULT NULL,
  `trang_thai` varchar(30) NOT NULL DEFAULT 'pending',
  `nguon_dat` varchar(30) NOT NULL DEFAULT 'website',
  PRIMARY KEY (`ma_lich_hen`),
  KEY `fk_khach_hang_lich_hen` (`ma_khach_hang`),
  KEY `fk_nhan_vien_lich_hen` (`ma_nhan_vien`),
  CONSTRAINT `fk_khach_hang_lich_hen` FOREIGN KEY (`ma_khach_hang`) REFERENCES `khach_hang` (`ma_khach_hang`) ON DELETE CASCADE,
  CONSTRAINT `fk_nhan_vien_lich_hen` FOREIGN KEY (`ma_nhan_vien`) REFERENCES `nhan_vien` (`ma_nhan_vien`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng dịch vụ đã đặt
CREATE TABLE `dich_vu_dat` (
  `ma_lich_hen` int(5) NOT NULL,
  `ma_dich_vu` int(5) NOT NULL,
  PRIMARY KEY (`ma_lich_hen`,`ma_dich_vu`),
  KEY `fk_dich_vu_dich_vu_dat` (`ma_dich_vu`),
  CONSTRAINT `fk_lich_hen_dich_vu_dat` FOREIGN KEY (`ma_lich_hen`) REFERENCES `lich_hen` (`ma_lich_hen`) ON DELETE CASCADE,
  CONSTRAINT `fk_dich_vu_dich_vu_dat` FOREIGN KEY (`ma_dich_vu`) REFERENCES `dich_vu` (`ma_dich_vu`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng lịch làm việc
CREATE TABLE `lich_lam_viec` (
  `ma` int(5) NOT NULL AUTO_INCREMENT,
  `ma_nhan_vien` int(2) NOT NULL,
  `ma_ngay` tinyint(1) NOT NULL,
  `gio_bat_dau` time NOT NULL,
  `gio_ket_thuc` time NOT NULL,
  PRIMARY KEY (`ma`),
  KEY `fk_nhan_vien_lich_lam_viec` (`ma_nhan_vien`),
  CONSTRAINT `fk_nhan_vien_lich_lam_viec` FOREIGN KEY (`ma_nhan_vien`) REFERENCES `nhan_vien` (`ma_nhan_vien`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 2. THU NGÂN (POS) & KHO
-- ============================================================

-- Bảng đơn hàng
CREATE TABLE `don_hang` (
  `ma_don_hang` int(11) NOT NULL AUTO_INCREMENT,
  `ma_don` varchar(20) DEFAULT NULL,
  `ma_khach_hang` int(11) DEFAULT NULL,
  `ma_lich_hen` int(11) DEFAULT NULL,
  `tong_truoc_giam` int(11) NOT NULL DEFAULT 0,
  `giam_gia` int(11) NOT NULL DEFAULT 0,
  `tong_cong` int(11) NOT NULL DEFAULT 0,
  `phuong_thuc_thanh_toan` varchar(30) NOT NULL DEFAULT 'cash',
  `trang_thai` varchar(20) NOT NULL DEFAULT 'completed',
  `ghi_chu` varchar(255) DEFAULT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ma_don_hang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng chi tiết đơn hàng
CREATE TABLE `chi_tiet_don_hang` (
  `ma_chi_tiet` int(11) NOT NULL AUTO_INCREMENT,
  `ma_don_hang` int(11) NOT NULL,
  `loai` varchar(20) NOT NULL,
  `ma_tham_chieu` int(11) NOT NULL,
  `ten` varchar(100) NOT NULL,
  `so_luong` int(11) NOT NULL DEFAULT 1,
  `don_gia` int(11) NOT NULL DEFAULT 0,
  `tong_dong` int(11) NOT NULL DEFAULT 0,
  `ma_nhan_vien` int(11) DEFAULT NULL,
  `giam_gia_dong` int(11) NOT NULL DEFAULT 0,
  `giam_gia_phan_tram` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ma_chi_tiet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng sản phẩm
CREATE TABLE `san_pham` (
  `ma_san_pham` int(11) NOT NULL AUTO_INCREMENT,
  `ten_san_pham` varchar(100) NOT NULL,
  `ma_sku` varchar(50) DEFAULT NULL,
  `don_vi` varchar(20) DEFAULT 'cai',
  `gia_ban` int(11) NOT NULL DEFAULT 0,
  `gia_nhap` int(11) NOT NULL DEFAULT 0,
  `so_luong_ton` int(11) NOT NULL DEFAULT 0,
  `so_luong_toi_thieu` int(11) NOT NULL DEFAULT 5,
  `hoat_dong` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`ma_san_pham`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. NHÂN SỰ: LƯƠNG, HOA HỒNG
-- ============================================================

-- Bảng chi tiết hoa hồng (đơn giản hóa - chỉ cần 1 bảng)
CREATE TABLE `chi_tiet_hoa_hong` (
  `ma_chi_tiet` int(11) NOT NULL AUTO_INCREMENT,
  `ma_nhan_vien` int(11) NOT NULL,
  `loai_doi_tuong` enum('dich_vu','san_pham') NOT NULL,
  `ma_doi_tuong` int(11) NOT NULL,
  `phan_tram_hoa_hong` decimal(5,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`ma_chi_tiet`),
  UNIQUE KEY `uk_nhan_vien_doi_tuong` (`ma_nhan_vien`,`loai_doi_tuong`,`ma_doi_tuong`),
  KEY `idx_nhan_vien` (`ma_nhan_vien`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 4. THU CHI
-- ============================================================

-- Bảng chi phí
CREATE TABLE `chi_phi` (
  `ma_chi_phi` int(11) NOT NULL AUTO_INCREMENT,
  `danh_muc` varchar(50) NOT NULL,
  `so_tien` int(11) NOT NULL,
  `ngay_chi` date NOT NULL,
  `ghi_chu` varchar(255) DEFAULT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ma_chi_phi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. DỮ LIỆU MẪU
-- ============================================================

-- Quản trị viên (admin / 123456789)
INSERT INTO `quan_tri_vien` (`ma_quan_tri`, `ten_dang_nhap`, `email`, `ho_ten`, `mat_khau`) VALUES
(1, 'admin', 'admin.admin@gmail.com', 'Admin Admin', 'f7c3bc1d808e04732adf679965ccc34ca7ae3441');

-- Danh mục dịch vụ
INSERT INTO `danh_muc_dich_vu` (`ma_danh_muc`, `ten_danh_muc`) VALUES
(2, 'Cạo râu'),
(3, 'Mặt nạ'),
(4, 'Chưa phân loại');

-- Khách hàng
INSERT INTO `khach_hang` (`ma_khach_hang`, `ten`, `ho_dem`, `so_dien_thoai`, `email`) VALUES
(1, 'Dennis', 'S Embry', '651-779-6791', 'dennis_embry@gmail.com'),
(2, 'Bonnie', 'A Rivera', '714-327-5825', 'bonnie_rivera@yahoo.fr'),
(13, 'Driss', 'Jabiri', '0789342481', 'driss.jabiri@gmail.com');

-- Nhân viên
INSERT INTO `nhan_vien` (`ma_nhan_vien`, `ten`, `ho_dem`, `so_dien_thoai`, `email`) VALUES
(1, 'RJ', 'Casillan', '', ''),
(2, 'K', 'Fades', '', ''),
(3, 'Santino', 'Tesoro', '', '');

-- Dịch vụ
INSERT INTO `dich_vu` (`ma_dich_vu`, `ten_dich_vu`, `mo_ta`, `gia`, `thoi_luong`, `ma_danh_muc`) VALUES
(1, 'Cắt tóc', 'Thợ cắt tóc là người chuyên nghiệp cắt, tạo kiểu và chăm sóc tóc cho nam giới', 210000, 20, 4),
(2, 'Tạo kiểu tóc', 'Thợ cắt tóc là người chuyên nghiệp cắt, tạo kiểu và chăm sóc tóc cho nam giới', 90000, 15, 4),
(3, 'Tỉa tóc', 'Thợ cắt tóc là người chuyên nghiệp cắt, tạo kiểu và chăm sóc tóc cho nam giới', 100000, 10, 4),
(4, 'Cạo râu sạch', 'Thợ cắt tóc là người chuyên nghiệp cắt, tạo kiểu và chăm sóc tóc cho nam giới', 200000, 20, 2),
(5, 'Tỉa râu', 'Thợ cắt tóc là người chuyên nghiệp cắt, tạo kiểu và chăm sóc tóc cho nam giới', 200000, 15, 2),
(6, 'Cạo mượt', 'Thợ cắt tóc là người chuyên nghiệp cắt, tạo kiểu và chăm sóc tóc cho nam giới', 150000, 20, 2),
(7, 'Mặt nạ trắng', 'Thợ cắt tóc là người chuyên nghiệp cắt, tạo kiểu và chăm sóc tóc cho nam giới', 160000, 15, 3),
(8, 'Làm sạch mặt', 'Thợ cắt tóc là người chuyên nghiệp cắt, tạo kiểu và chăm sóc tóc cho nam giới', 200000, 20, 3),
(9, 'Tẩy sáng', 'Thợ cắt tóc là người chuyên nghiệp cắt, tạo kiểu và chăm sóc tóc cho nam giới', 140000, 20, 3);

-- Lịch hẹn
INSERT INTO `lich_hen` (`ma_lich_hen`, `ngay_tao`, `ma_khach_hang`, `ma_nhan_vien`, `thoi_gian_bat_dau`, `thoi_gian_ket_thuc_du_kien`, `da_huy`, `ly_do_huy`, `trang_thai`, `nguon_dat`) VALUES
(12, '2024-02-04 00:04:00', 13, 3, '2024-02-12 09:30:00', '2024-02-12 10:00:00', 0, NULL, 'confirmed', 'website');

-- Dịch vụ đã đặt
INSERT INTO `dich_vu_dat` (`ma_lich_hen`, `ma_dich_vu`) VALUES
(12, 1),
(12, 3);

-- Lịch làm việc
INSERT INTO `lich_lam_viec` (`ma`, `ma_nhan_vien`, `ma_ngay`, `gio_bat_dau`, `gio_ket_thuc`) VALUES
(29, 3, 1, '09:00:00', '18:00:00'),
(30, 3, 7, '09:00:00', '17:00:00'),
(38, 2, 1, '09:00:00', '17:00:00'),
(39, 2, 6, '09:00:00', '18:00:00'),
(40, 2, 7, '09:00:00', '18:00:00'),
(41, 1, 1, '09:00:00', '18:00:00'),
(42, 1, 2, '15:00:00', '22:00:00'),
(43, 1, 3, '09:00:00', '18:00:00'),
(44, 1, 4, '09:00:00', '20:00:00'),
(45, 1, 5, '09:00:00', '14:00:00'),
(46, 1, 7, '09:00:00', '18:00:00');

-- Sản phẩm
INSERT INTO `san_pham` (`ten_san_pham`, `ma_sku`, `don_vi`, `gia_ban`, `gia_nhap`, `so_luong_ton`, `so_luong_toi_thieu`) VALUES
('Dầu gội nam', 'SP001', 'chai', 150000, 80000, 20, 5),
('Sáp vuốt tóc', 'SP002', 'hộp', 120000, 60000, 8, 3);

-- ============================================================
-- HOÀN TẤT — Database: barbershop
-- Admin: admin / 123456789
-- ============================================================
