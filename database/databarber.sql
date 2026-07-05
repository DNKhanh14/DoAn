
--  Hướng dẫn Import vào phpMyAdmin:
--    1. Mở phpMyAdmin (http://localhost/phpmyadmin)
--    2. Không cần chọn database trước. Chọn trực tiếp tab "Import" (Nhập) ở menu ngang phía trên.
--    3. Nhấn "Choose File" (Chọn tệp) -> Chọn file "databarber.sql" này.
--    4. Cuộn xuống dưới cùng và nhấn nút "Import" (Thực hiện).
--    5. Hệ thống sẽ tự động tạo cơ sở dữ liệu "barbershop" và cấu trúc bảng.
--
--  Thông tin đăng nhập Admin mặc định:
--    - Tên đăng nhập: admin
--    - Mật khẩu: 123456789
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

-- Người dùng hệ thống (mọi tài khoản đăng nhập: admin, quản lý, lễ tân, thợ...)
CREATE TABLE `nguoi_dung` (
  `ma_nguoi_dung` int(5)       NOT NULL AUTO_INCREMENT,
  `ten_dang_nhap` varchar(50)  NOT NULL,
  `email`         varchar(50)  NOT NULL,
  `ho_ten`        varchar(50)  NOT NULL,
  `mat_khau`      varchar(255) NOT NULL,
  `ma_chuc_vu`    int(11)      NOT NULL,
  `ma_nhan_vien`  int(2)       DEFAULT NULL,
  `reset_token`   varchar(64)  DEFAULT NULL,
  `reset_expires` datetime     DEFAULT NULL,
  PRIMARY KEY (`ma_nguoi_dung`),
  UNIQUE KEY `uk_username_email` (`ten_dang_nhap`, `email`),
  KEY `fk_nd_nhan_vien` (`ma_nhan_vien`),
  KEY `fk_nd_chuc_vu`   (`ma_chuc_vu`),
  CONSTRAINT `fk_nd_nhan_vien`
    FOREIGN KEY (`ma_nhan_vien`) REFERENCES `nhan_vien`   (`ma_nhan_vien`) ON DELETE SET NULL,
  CONSTRAINT `fk_nd_chuc_vu`
    FOREIGN KEY (`ma_chuc_vu`)   REFERENCES `chuc_vu_quyen` (`ma_chuc_vu`) ON DELETE RESTRICT
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

-- Nhân viên
CREATE TABLE `nhan_vien` (
  `ma_nhan_vien`  int(2)        NOT NULL AUTO_INCREMENT,
  `ten`           varchar(20)   NOT NULL,
  `ho_dem`        varchar(20)   NOT NULL,
  `so_dien_thoai` varchar(30)   NOT NULL,
  `email`         varchar(50)   NOT NULL,
  `ma_chuc_vu`    int(11)       DEFAULT NULL,
  `luong_co_ban`  decimal(12,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`ma_nhan_vien`),
  KEY `fk_nv_chuc_vu` (`ma_chuc_vu`),
  CONSTRAINT `fk_nv_chuc_vu`
    FOREIGN KEY (`ma_chuc_vu`) REFERENCES `chuc_vu_quyen` (`ma_chuc_vu`) ON DELETE SET NULL
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
  `ma_lich_hen`                int(5)      NOT NULL AUTO_INCREMENT,
  `ngay_tao`                   timestamp   NOT NULL DEFAULT current_timestamp(),
  `ma_khach_hang`              int(5)      NOT NULL,
  `ma_nhan_vien`               int(2)      NOT NULL,
  `thoi_gian_bat_dau`          timestamp   NOT NULL DEFAULT current_timestamp(),
  `thoi_gian_ket_thuc_du_kien` timestamp   NOT NULL DEFAULT current_timestamp(),
  `da_huy`                     tinyint(1)  NOT NULL DEFAULT 0,
  `ly_do_huy`                  text        DEFAULT NULL,
  `trang_thai`                 varchar(30) NOT NULL DEFAULT 'pending',
  `nguon_dat`                  varchar(30) NOT NULL DEFAULT 'website',
  PRIMARY KEY (`ma_lich_hen`),
  KEY `fk_khach_hang_lich_hen` (`ma_khach_hang`),
  KEY `fk_nhan_vien_lich_hen`  (`ma_nhan_vien`),
  CONSTRAINT `fk_khach_hang_lich_hen`
    FOREIGN KEY (`ma_khach_hang`) REFERENCES `khach_hang`  (`ma_khach_hang`) ON DELETE CASCADE,
  CONSTRAINT `fk_nhan_vien_lich_hen`
    FOREIGN KEY (`ma_nhan_vien`)  REFERENCES `nhan_vien`   (`ma_nhan_vien`)  ON DELETE CASCADE
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
  `ma_don_hang`            int(11)      NOT NULL AUTO_INCREMENT,
  `ma_khach_hang`          int(5)       DEFAULT NULL,
  `ma_lich_hen`            int(5)       DEFAULT NULL,
  `tong_truoc_giam`        int(11)      NOT NULL DEFAULT 0,
  `giam_gia`               int(11)      NOT NULL DEFAULT 0,
  `tong_cong`              int(11)      NOT NULL DEFAULT 0,
  `phuong_thuc_thanh_toan` varchar(30)  NOT NULL DEFAULT 'cash',
  `trang_thai`             varchar(20)  NOT NULL DEFAULT 'completed',
  `ghi_chu`                varchar(255) DEFAULT NULL,
  `ngay_tao`               timestamp    NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ma_don_hang`),
  KEY `fk_don_hang_khach_hang` (`ma_khach_hang`),
  KEY `fk_don_hang_lich_hen` (`ma_lich_hen`),
  CONSTRAINT `fk_don_hang_khach_hang`
    FOREIGN KEY (`ma_khach_hang`) REFERENCES `khach_hang` (`ma_khach_hang`) ON DELETE SET NULL,
  CONSTRAINT `fk_don_hang_lich_hen`
    FOREIGN KEY (`ma_lich_hen`) REFERENCES `lich_hen` (`ma_lich_hen`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Chi tiết đơn hàng
-- Mỗi dòng là 1 mặt hàng trong đơn: điền ma_dich_vu HOẶC ma_san_pham, cái còn lại NULL
CREATE TABLE `chi_tiet_don_hang` (
  `ma_chi_tiet`        int(11)      NOT NULL AUTO_INCREMENT,
  `ma_don_hang`        int(11)      NOT NULL,
  `ma_dich_vu`         int(5)       DEFAULT NULL COMMENT 'Điền khi bán dịch vụ',
  `ma_san_pham`        int(11)      DEFAULT NULL COMMENT 'Điền khi bán sản phẩm',
  `ten`                varchar(100) NOT NULL     COMMENT 'Snapshot tên tại thời điểm bán',
  `so_luong`           int(11)      NOT NULL DEFAULT 1,
  `don_gia`            int(11)      NOT NULL DEFAULT 0 COMMENT 'Snapshot giá tại thời điểm bán',
  `tong_dong`          int(11)      NOT NULL DEFAULT 0,
  `ma_nhan_vien`       int(2)       DEFAULT NULL,
  `giam_gia_dong`      int(11)      NOT NULL DEFAULT 0,
  `giam_gia_phan_tram` tinyint(1)   NOT NULL DEFAULT 0,
  PRIMARY KEY (`ma_chi_tiet`),
  KEY `fk_ctdh_don_hang`   (`ma_don_hang`),
  KEY `fk_ctdh_dich_vu`    (`ma_dich_vu`),
  KEY `fk_ctdh_san_pham`   (`ma_san_pham`),
  KEY `fk_ctdh_nhan_vien`  (`ma_nhan_vien`),
  CONSTRAINT `fk_ctdh_don_hang`
    FOREIGN KEY (`ma_don_hang`)  REFERENCES `don_hang`  (`ma_don_hang`)  ON DELETE CASCADE,
  CONSTRAINT `fk_ctdh_dich_vu`
    FOREIGN KEY (`ma_dich_vu`)   REFERENCES `dich_vu`   (`ma_dich_vu`)   ON DELETE SET NULL,
  CONSTRAINT `fk_ctdh_san_pham`
    FOREIGN KEY (`ma_san_pham`)  REFERENCES `san_pham`  (`ma_san_pham`)  ON DELETE SET NULL,
  CONSTRAINT `fk_ctdh_nhan_vien`
    FOREIGN KEY (`ma_nhan_vien`) REFERENCES `nhan_vien` (`ma_nhan_vien`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sản phẩm / kho
CREATE TABLE `san_pham` (
  `ma_san_pham`        int(11)      NOT NULL AUTO_INCREMENT,
  `ten_san_pham`       varchar(100) NOT NULL,
  `don_vi`             varchar(20)  DEFAULT 'cai',
  `gia_ban`            int(11)      NOT NULL DEFAULT 0,
  `gia_nhap`           int(11)      NOT NULL DEFAULT 0,
  `so_luong_ton`       int(11)      NOT NULL DEFAULT 0,
  `so_luong_toi_thieu` int(11)      NOT NULL DEFAULT 5,
  `hoat_dong`          tinyint(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (`ma_san_pham`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PHẦN 3 — NHÂN SỰ: HOA HỒNG, LƯƠNG
-- ============================================================

-- Hoa hồng theo nhân viên / dịch vụ / sản phẩm
-- `loai_doi_tuong` + `ma_doi_tuong` giữ nguyên cho PHP backward-compat
-- `ma_san_pham` là FK thực để diagram hiển thị liên kết với bảng san_pham
CREATE TABLE `chi_tiet_hoa_hong` (
  `ma_chi_tiet`        int(11)  NOT NULL AUTO_INCREMENT,
  `ma_nhan_vien`       int(2)   NOT NULL,
  `loai_doi_tuong`     enum('dich_vu','san_pham') NOT NULL,
  `ma_doi_tuong`       int(11)  NOT NULL,
  `ma_san_pham`        int(11)  DEFAULT NULL COMMENT 'FK tới san_pham khi loai_doi_tuong=san_pham',
  `phan_tram_hoa_hong` decimal(5,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`ma_chi_tiet`),
  UNIQUE KEY `uk_nhan_vien_doi_tuong` (`ma_nhan_vien`, `loai_doi_tuong`, `ma_doi_tuong`),
  KEY `idx_nhan_vien`                      (`ma_nhan_vien`),
  KEY `fk_chi_tiet_hoa_hong_san_pham`      (`ma_san_pham`),
  CONSTRAINT `fk_chi_tiet_hoa_hong_nhan_vien`
    FOREIGN KEY (`ma_nhan_vien`) REFERENCES `nhan_vien` (`ma_nhan_vien`) ON DELETE CASCADE,
  CONSTRAINT `fk_chi_tiet_hoa_hong_san_pham`
    FOREIGN KEY (`ma_san_pham`)  REFERENCES `san_pham`  (`ma_san_pham`)  ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



-- Nghỉ phép
CREATE TABLE `nghi_phep` (
  `ma`           int(11)     NOT NULL AUTO_INCREMENT,
  `ma_nhan_vien` int(2)      NOT NULL,
  `tu_ngay`      date        NOT NULL,
  `den_ngay`     date        NOT NULL,
  `so_ngay_nghi` decimal(4,1) NOT NULL DEFAULT 1.0,
  `trang_thai`   enum('co_phep','khong_phep') NOT NULL DEFAULT 'co_phep',
  `ghi_chu`      varchar(255) DEFAULT NULL,
  `ngay_tao`     timestamp   NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ma`),
  KEY `idx_nhan_vien` (`ma_nhan_vien`),
  CONSTRAINT `fk_nghi_phep_nhan_vien`
    FOREIGN KEY (`ma_nhan_vien`) REFERENCES `nhan_vien` (`ma_nhan_vien`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thưởng / phạt
CREATE TABLE `thuong_phat` (
  `ma`           int(11)  NOT NULL AUTO_INCREMENT,
  `ma_nhan_vien` int(2)   NOT NULL,
  `ngay_ghi`     date     NOT NULL,
  `loai_ghi`     enum('bonus','penalty') NOT NULL DEFAULT 'bonus',
  `danh_muc`     varchar(100) NOT NULL DEFAULT '',
  `so_tien`      decimal(12,2) NOT NULL DEFAULT 0.00,
  `ghi_chu`      varchar(255) DEFAULT NULL,
  `ngay_tao`     timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ma`),
  KEY `idx_nhan_vien` (`ma_nhan_vien`),
  CONSTRAINT `fk_thuong_phat_nhan_vien`
    FOREIGN KEY (`ma_nhan_vien`) REFERENCES `nhan_vien` (`ma_nhan_vien`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thanh toán lương
CREATE TABLE `thanh_toan_luong` (
  `ma_thanh_toan`   int(11)       NOT NULL AUTO_INCREMENT,
  `ma_nhan_vien`    int(2)        NOT NULL,
  `loai_thanh_toan` varchar(30)   NOT NULL DEFAULT 'salary',
  `phuong_thuc`     varchar(20)   NOT NULL DEFAULT 'cash',
  `so_tien`         decimal(12,2) NOT NULL DEFAULT 0.00,
  `ghi_chu`         varchar(255)  DEFAULT NULL,
  `ky_luong`        varchar(20)   DEFAULT NULL,
  `ngay_thanh_toan` date          DEFAULT NULL,
  `ngay_tao`        timestamp     NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ma_thanh_toan`),
  KEY `idx_nhan_vien` (`ma_nhan_vien`),
  CONSTRAINT `fk_thanh_toan_luong_nhan_vien`
    FOREIGN KEY (`ma_nhan_vien`) REFERENCES `nhan_vien` (`ma_nhan_vien`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PHẦN 4 — THU CHI
-- ============================================================

-- Chi phí vận hành
CREATE TABLE `chi_phi` (
  `ma_chi_phi` int(11)      NOT NULL AUTO_INCREMENT,
  `danh_muc`   varchar(50)  NOT NULL,
  `so_tien`    int(11)      NOT NULL,
  `ngay_chi`   date         NOT NULL,
  `ghi_chu`    varchar(255) DEFAULT NULL,
  `ngay_tao`   timestamp    NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ma_chi_phi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PHẦN 5 — PHÂN QUYỀN & CHỨC VỤ
-- ============================================================

-- Chức vụ + quyền (1 dòng = 1 chức vụ — FK từ nhan_vien và nguoi_dung)
--   Mỗi cột module: 1=có quyền, 0=không có quyền
CREATE TABLE `chuc_vu_quyen` (
  `ma_chuc_vu`  int(11)     NOT NULL AUTO_INCREMENT,
  `ten_chuc_vu` varchar(50) NOT NULL,
  `dashboard`   tinyint(1)  NOT NULL DEFAULT 0,
  `booking`     tinyint(1)  NOT NULL DEFAULT 0,
  `pos`         tinyint(1)  NOT NULL DEFAULT 0,
  `crm`         tinyint(1)  NOT NULL DEFAULT 0,
  `services`    tinyint(1)  NOT NULL DEFAULT 0,
  `inventory`   tinyint(1)  NOT NULL DEFAULT 0,
  `employees`   tinyint(1)  NOT NULL DEFAULT 0,
  `hr`          tinyint(1)  NOT NULL DEFAULT 0,
  `reports`     tinyint(1)  NOT NULL DEFAULT 0,
  `accounts`    tinyint(1)  NOT NULL DEFAULT 0,
  PRIMARY KEY (`ma_chuc_vu`),
  UNIQUE KEY `uk_ten_chuc_vu` (`ten_chuc_vu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 

-- ============================================================
--  HOÀN TẤT
--  Database    : barbershop
--  Admin       : admin / 123456789
--  Bảng        : 16 bảng
--  Chức vụ     : super_admin(1) | Quản lý(2) | Lễ tân(3) | Thợ chính(4) | Thợ phụ(5)
--  Liên kết CV : nhan_vien.ma_chuc_vu → chuc_vu_quyen.ma_chuc_vu (FK)
--              : nguoi_dung.ma_chuc_vu → chuc_vu_quyen.ma_chuc_vu (FK)
-- ============================================================
