-- ============================================================
--  BARBERSHOP DATABASE — FILE CÀI ĐẶT HOÀN CHỈNH
--  Phiên bản: 2.0
--  Gộp từ: data_tieng_viet.sql + upgrade_hr_payroll.sql
--
--  Hướng dẫn:
--    1. Mở phpMyAdmin → tab SQL
--    2. Paste toàn bộ nội dung file này và chạy
--    3. Tài khoản admin: admin / 123456789
-- ============================================================

SET SQL_MODE        = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone       = "+00:00";
SET NAMES           utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- TẠO DATABASE
-- ============================================================

DROP DATABASE IF EXISTS `barbershop`;
CREATE DATABASE `barbershop`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `barbershop`;

-- ============================================================
-- PHẦN 1 — BẢNG CƠ BẢN
-- ============================================================

-- Danh mục dịch vụ
CREATE TABLE `danh_muc_dich_vu` (
  `ma_danh_muc`  int(2)      NOT NULL AUTO_INCREMENT,
  `ten_danh_muc` varchar(50) NOT NULL,
  PRIMARY KEY (`ma_danh_muc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Quản trị viên
CREATE TABLE `quan_tri_vien` (
  `ma_quan_tri`  int(5)       NOT NULL AUTO_INCREMENT,
  `ten_dang_nhap` varchar(50) NOT NULL,
  `email`         varchar(50) NOT NULL,
  `ho_ten`        varchar(50) NOT NULL,
  `mat_khau`      varchar(255) NOT NULL,
  PRIMARY KEY (`ma_quan_tri`),
  UNIQUE KEY `uk_username_email` (`ten_dang_nhap`, `email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Khách hàng
CREATE TABLE `khach_hang` (
  `ma_khach_hang` int(5)      NOT NULL AUTO_INCREMENT,
  `ten`           varchar(30) NOT NULL,
  `ho_dem`        varchar(30) NOT NULL,
  `so_dien_thoai` varchar(30) NOT NULL,
  `email`         varchar(50) NOT NULL,
  `ngay_sinh`     date        DEFAULT NULL,
  PRIMARY KEY (`ma_khach_hang`),
  UNIQUE KEY `uk_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Nhân viên (bao gồm sẵn cột chuc_vu)
CREATE TABLE `nhan_vien` (
  `ma_nhan_vien`  int(2)       NOT NULL AUTO_INCREMENT,
  `ten`           varchar(20)  NOT NULL,
  `ho_dem`        varchar(20)  NOT NULL,
  `so_dien_thoai` varchar(30)  NOT NULL,
  `email`         varchar(50)  NOT NULL,
  `chuc_vu`       varchar(100) DEFAULT NULL,
  PRIMARY KEY (`ma_nhan_vien`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dịch vụ
CREATE TABLE `dich_vu` (
  `ma_dich_vu`  int(5)       NOT NULL AUTO_INCREMENT,
  `ten_dich_vu` varchar(50)  NOT NULL,
  `mo_ta`       varchar(255) NOT NULL,
  `gia`         int(11)      NOT NULL,
  `thoi_luong`  int(5)       NOT NULL,
  `ma_danh_muc` int(2)       NOT NULL,
  PRIMARY KEY (`ma_dich_vu`),
  KEY `fk_danh_muc_dich_vu` (`ma_danh_muc`),
  CONSTRAINT `fk_danh_muc_dich_vu`
    FOREIGN KEY (`ma_danh_muc`) REFERENCES `danh_muc_dich_vu` (`ma_danh_muc`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Lịch hẹn
CREATE TABLE `lich_hen` (
  `ma_lich_hen`              int(5)      NOT NULL AUTO_INCREMENT,
  `ngay_tao`                 timestamp   NOT NULL DEFAULT current_timestamp(),
  `ma_khach_hang`            int(5)      NOT NULL,
  `ma_nhan_vien`             int(2)      NOT NULL,
  `thoi_gian_bat_dau`        timestamp   NOT NULL DEFAULT current_timestamp(),
  `thoi_gian_ket_thuc_du_kien` timestamp NOT NULL DEFAULT current_timestamp(),
  `da_huy`                   tinyint(1)  NOT NULL DEFAULT 0,
  `ly_do_huy`                text        DEFAULT NULL,
  `trang_thai`               varchar(30) NOT NULL DEFAULT 'pending',
  `nguon_dat`                varchar(30) NOT NULL DEFAULT 'website',
  PRIMARY KEY (`ma_lich_hen`),
  KEY `fk_khach_hang_lich_hen` (`ma_khach_hang`),
  KEY `fk_nhan_vien_lich_hen`  (`ma_nhan_vien`),
  CONSTRAINT `fk_khach_hang_lich_hen`
    FOREIGN KEY (`ma_khach_hang`) REFERENCES `khach_hang` (`ma_khach_hang`) ON DELETE CASCADE,
  CONSTRAINT `fk_nhan_vien_lich_hen`
    FOREIGN KEY (`ma_nhan_vien`)  REFERENCES `nhan_vien`  (`ma_nhan_vien`)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dịch vụ đã đặt
CREATE TABLE `dich_vu_dat` (
  `ma_lich_hen` int(5) NOT NULL,
  `ma_dich_vu`  int(5) NOT NULL,
  PRIMARY KEY (`ma_lich_hen`, `ma_dich_vu`),
  KEY `fk_dich_vu_dich_vu_dat` (`ma_dich_vu`),
  CONSTRAINT `fk_lich_hen_dich_vu_dat`
    FOREIGN KEY (`ma_lich_hen`) REFERENCES `lich_hen` (`ma_lich_hen`) ON DELETE CASCADE,
  CONSTRAINT `fk_dich_vu_dich_vu_dat`
    FOREIGN KEY (`ma_dich_vu`)  REFERENCES `dich_vu`  (`ma_dich_vu`)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- PHẦN 2 — THU NGÂN (POS) & KHO
-- ============================================================

-- Đơn hàng
CREATE TABLE `don_hang` (
  `ma_don_hang`           int(11)     NOT NULL AUTO_INCREMENT,
  `ma_don`                varchar(20) DEFAULT NULL,
  `ma_khach_hang`         int(11)     DEFAULT NULL,
  `ma_lich_hen`           int(11)     DEFAULT NULL,
  `tong_truoc_giam`       int(11)     NOT NULL DEFAULT 0,
  `giam_gia`              int(11)     NOT NULL DEFAULT 0,
  `tong_cong`             int(11)     NOT NULL DEFAULT 0,
  `phuong_thuc_thanh_toan` varchar(30) NOT NULL DEFAULT 'cash',
  `trang_thai`            varchar(20) NOT NULL DEFAULT 'completed',
  `ghi_chu`               varchar(255) DEFAULT NULL,
  `ngay_tao`              timestamp   NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ma_don_hang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Chi tiết đơn hàng
CREATE TABLE `chi_tiet_don_hang` (
  `ma_chi_tiet`        int(11)     NOT NULL AUTO_INCREMENT,
  `ma_don_hang`        int(11)     NOT NULL,
  `loai`               varchar(20) NOT NULL,
  `ma_tham_chieu`      int(11)     NOT NULL,
  `ten`                varchar(100) NOT NULL,
  `so_luong`           int(11)     NOT NULL DEFAULT 1,
  `don_gia`            int(11)     NOT NULL DEFAULT 0,
  `tong_dong`          int(11)     NOT NULL DEFAULT 0,
  `ma_nhan_vien`       int(11)     DEFAULT NULL,
  `giam_gia_dong`      int(11)     NOT NULL DEFAULT 0,
  `giam_gia_phan_tram` tinyint(1)  NOT NULL DEFAULT 0,
  PRIMARY KEY (`ma_chi_tiet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sản phẩm / kho
CREATE TABLE `san_pham` (
  `ma_san_pham`       int(11)     NOT NULL AUTO_INCREMENT,
  `ten_san_pham`      varchar(100) NOT NULL,
  `ma_sku`            varchar(50) DEFAULT NULL,
  `don_vi`            varchar(20) DEFAULT 'cai',
  `gia_ban`           int(11)     NOT NULL DEFAULT 0,
  `gia_nhap`          int(11)     NOT NULL DEFAULT 0,
  `so_luong_ton`      int(11)     NOT NULL DEFAULT 0,
  `so_luong_toi_thieu` int(11)    NOT NULL DEFAULT 5,
  `hoat_dong`         tinyint(1)  NOT NULL DEFAULT 1,
  PRIMARY KEY (`ma_san_pham`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PHẦN 3 — NHÂN SỰ: HOA HỒNG, LƯƠNG
-- ============================================================

-- Hoa hồng theo nhân viên / dịch vụ / sản phẩm
CREATE TABLE `chi_tiet_hoa_hong` (
  `ma_chi_tiet`        int(11)  NOT NULL AUTO_INCREMENT,
  `ma_nhan_vien`       int(11)  NOT NULL,
  `loai_doi_tuong`     enum('dich_vu','san_pham') NOT NULL,
  `ma_doi_tuong`       int(11)  NOT NULL,
  `phan_tram_hoa_hong` decimal(5,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`ma_chi_tiet`),
  UNIQUE KEY `uk_nhan_vien_doi_tuong` (`ma_nhan_vien`, `loai_doi_tuong`, `ma_doi_tuong`),
  KEY `idx_nhan_vien` (`ma_nhan_vien`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Nghỉ phép
CREATE TABLE `nghi_phep` (
  `ma`            int(11)     NOT NULL AUTO_INCREMENT,
  `ma_nhan_vien`  int(11)     NOT NULL,
  `tu_ngay`       date        NOT NULL,
  `den_ngay`      date        NOT NULL,
  `so_ngay_nghi`  decimal(4,1) NOT NULL DEFAULT 1.0,
  `trang_thai`    enum('co_phep','khong_phep') NOT NULL DEFAULT 'co_phep',
  `ghi_chu`       varchar(255) DEFAULT NULL,
  `ngay_tao`      timestamp   NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ma`),
  KEY `idx_nhan_vien` (`ma_nhan_vien`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thưởng / phạt
CREATE TABLE `thuong_phat` (
  `ma`           int(11)  NOT NULL AUTO_INCREMENT,
  `ma_nhan_vien` int(11)  NOT NULL,
  `ngay_ghi`     date     NOT NULL,
  `loai_ghi`     enum('bonus','penalty') NOT NULL DEFAULT 'bonus',
  `danh_muc`     varchar(100) NOT NULL DEFAULT '',
  `so_tien`      decimal(12,2) NOT NULL DEFAULT 0.00,
  `ghi_chu`      varchar(255) DEFAULT NULL,
  `ngay_tao`     timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ma`),
  KEY `idx_nhan_vien` (`ma_nhan_vien`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thanh toán lương
CREATE TABLE `thanh_toan_luong` (
  `ma_thanh_toan`  int(11)     NOT NULL AUTO_INCREMENT,
  `ma_nhan_vien`   int(11)     NOT NULL,
  `loai_thanh_toan` varchar(30) NOT NULL DEFAULT 'salary',
  `phuong_thuc`    varchar(20) NOT NULL DEFAULT 'cash',
  `so_tien`        decimal(12,2) NOT NULL DEFAULT 0.00,
  `ghi_chu`        varchar(255) DEFAULT NULL,
  `ngay_tao`       timestamp   NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ma_thanh_toan`),
  KEY `idx_nhan_vien` (`ma_nhan_vien`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cài đặt lương cơ bản theo nhân viên
CREATE TABLE `cai_dat_luong` (
  `ma_nhan_vien` int(11)       NOT NULL,
  `luong_co_ban` decimal(12,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`ma_nhan_vien`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PHẦN 4 — THU CHI
-- ============================================================

-- Chi phí vận hành
CREATE TABLE `chi_phi` (
  `ma_chi_phi` int(11)     NOT NULL AUTO_INCREMENT,
  `danh_muc`   varchar(50) NOT NULL,
  `so_tien`    int(11)     NOT NULL,
  `ngay_chi`   date        NOT NULL,
  `ghi_chu`    varchar(255) DEFAULT NULL,
  `ngay_tao`   timestamp   NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ma_chi_phi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PHẦN 5 — DỮ LIỆU MẪU
-- ============================================================

-- Quản trị viên (admin / 123456789)
INSERT INTO `quan_tri_vien` (`ma_quan_tri`, `ten_dang_nhap`, `email`, `ho_ten`, `mat_khau`) VALUES
(1, 'admin', 'admin.admin@gmail.com', 'Admin Admin', 'f7c3bc1d808e04732adf679965ccc34ca7ae3441');

-- Danh mục dịch vụ
INSERT INTO `danh_muc_dich_vu` (`ma_danh_muc`, `ten_danh_muc`) VALUES
(1, 'Cắt tóc'),
(2, 'Cạo râu'),
(3, 'Mặt'),
(4, 'Chưa phân loại');

-- Khách hàng mẫu
INSERT INTO `khach_hang` (`ma_khach_hang`, `ten`, `ho_dem`, `so_dien_thoai`, `email`) VALUES
(1, 'Nguyễn', 'Văn An',     '0901234567', 'nguyen.van.an@gmail.com'),
(2, 'Trần',   'Minh Tuấn',  '0912345678', 'tran.minh.tuan@gmail.com'),
(3, 'Lê',     'Hoàng Nam',  '0923456789', 'le.hoang.nam@gmail.com');

-- Nhân viên mẫu (có chức vụ)
INSERT INTO `nhan_vien` (`ma_nhan_vien`, `ten`, `ho_dem`, `so_dien_thoai`, `email`, `chuc_vu`) VALUES
(1, 'Minh', 'Thợ Barber',   '0909111222', 'barber1@shop.com', 'Thợ chính'),
(2, 'Tuấn', 'Cắt Tóc',      '0909333444', 'barber2@shop.com', 'Thợ chính'),
(3, 'Hùng', 'Trợ Lý',       '0909555666', 'barber3@shop.com', 'Thợ phụ');

-- Dịch vụ mẫu
INSERT INTO `dich_vu` (`ma_dich_vu`, `ten_dich_vu`, `mo_ta`, `gia`, `thoi_luong`, `ma_danh_muc`) VALUES
(1, 'Cắt gọi',      'Cắt tóc tạo kiểu theo yêu cầu, phù hợp khuôn mặt',      300000, 45, 1),
(2, 'Tạo kiểu tóc', 'Vuốt sáp, tạo kiểu hoàn chỉnh sau khi cắt',              90000,  15, 1),
(3, 'Tỉa tóc',      'Tỉa gọn tóc, không thay đổi kiểu dáng',                  100000, 20, 1),
(4, 'Cạo râu sạch', 'Cạo râu hoàn toàn bằng dao lam, làm sạch da',            200000, 25, 2),
(5, 'Tỉa râu',      'Tạo dáng râu gọn gàng theo ý muốn',                      150000, 15, 2),
(6, 'Cạo mượt',     'Cạo nhẹ nhàng, dưỡng ẩm sau cạo',                        150000, 20, 2),
(7, 'Mặt nạ trắng', 'Đắp mặt nạ dưỡng trắng, làm sáng da',                    160000, 20, 3),
(8, 'Làm sạch mặt', 'Tẩy tế bào chết, làm sạch sâu lỗ chân lông',             200000, 25, 3),
(9, 'Tẩy sáng',     'Liệu trình tẩy sáng da chuyên sâu',                       140000, 30, 3);



-- Sản phẩm mẫu
INSERT INTO `san_pham` (`ten_san_pham`, `ma_sku`, `don_vi`, `gia_ban`, `gia_nhap`, `so_luong_ton`, `so_luong_toi_thieu`) VALUES
('Dầu gội nam',   'SP001', 'chai', 150000, 80000,  20, 5),
('Sáp vuốt tóc',  'SP002', 'hộp',  120000, 60000,  15, 3),
('Dưỡng râu',     'SP003', 'tuýp',  80000, 40000,  10, 3),
('Wax tạo kiểu',  'SP004', 'hộp',  100000, 50000,   8, 3);

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
--  HOÀN TẤT
--  Database  : barbershop
--  Admin     : admin / 123456789
--  Bảng      : 16 bảng (cơ bản + POS + nhân sự + thu chi)
-- ============================================================
