# Hệ thống quản lý Barber Salon

Website đặt lịch cho khách và bảng quản trị cho tiệm tóc/barber, xây dựng bằng **PHP**, **MySQL** và kiến trúc **MVC**. Giao diện tiếng Việt, đơn vị tiền **VND**.

---

## Công nghệ

- HTML, CSS, Bootstrap
- JavaScript, jQuery, Ajax
- PHP 7.4+ (PDO)
- MySQL / MariaDB

---

## Cài đặt

### 1. Chuẩn bị

- Cài [XAMPP](https://www.apachefriends.org/) (hoặc Apache + PHP + MySQL)
- Đặt thư mục dự án vào `htdocs` (ví dụ: `c:\xampp\htdocs\barbershop-website-php-mysql-main\barbershop-website-php-mysql-main`)

### 2. Database

File duy nhất: **`db_barber.sql`** (thư mục gốc dự án).

#### Cài mới (chưa có database)

1. Mở trình duyệt: `http://localhost/phpmyadmin`
2. Tab **Import** (không cần chọn database trước)
3. **Chọn tệp** → `db_barber.sql` → **Thực hiện**

#### Thay database cũ bằng file SQL mới (đã có `barbershop`)

File `db_barber.sql` có dòng `DROP DATABASE IF EXISTS barbershop` — import **một lần** sẽ xóa toàn bộ dữ liệu cũ và tạo lại từ đầu.

1. **Sao lưu** (nếu cần giữ dữ liệu): phpMyAdmin → chọn `barbershop` → tab **Export** → **Thực hiện**
2. Tab **Import** (có thể đứng ở trang chủ phpMyAdmin hoặc đã chọn `barbershop`)
3. Chọn `db_barber.sql` → **Thực hiện**
4. Đợi thông báo thành công → F5 trang admin và đăng nhập lại

> **Cảnh báo:** Mọi hóa đơn, khách, lịch hẹn đã nhập sẽ **mất hết**. Chỉ import lại khi muốn reset hoặc đồng bộ schema mới.

Nếu import báo lỗi giới hạn dung lượng: tăng `upload_max_filesize` và `post_max_size` trong `php.ini`, hoặc chạy lệnh:

```bash
mysql -u root < db_barber.sql
```

(trong thư mục dự án, dùng đường dẫn `mysql` của XAMPP)

### 3. Cấu hình kết nối

Chỉnh file `app/config/config.php`:

```php
'host' => 'localhost',
'name' => 'barbershop',
'user' => 'root',
'pass' => '',
```

### 4. Truy cập

| Khu vực | URL (ví dụ XAMPP) |
|---------|-------------------|
| Trang khách | `http://localhost/barbershop-website-php-mysql-main/barbershop-website-php-mysql-main/` |
| Quản trị | `http://localhost/.../barber-admin/` |

**Đăng nhập admin mặc định** 

- Tên đăng nhập: `admin`
- Mật khẩu: `123456789`

Nên đổi mật khẩu ngay sau khi cài.

---

## Cấu trúc thư mục

```
app/
├── Admin/Controllers/   # Controller trang quản trị
├── Controllers/         # Controller trang công khai
├── Models/              # Truy vấn database
├── Views/               # Giao diện HTML
│   ├── admin/           # View quản trị
│   └── layouts/         # Layout trang khách
├── routes/
│   ├── web.php          # Route công khai
│   └── admin.php        # Route quản trị
├── Helpers/             # Hàm tiện ích (format VND, route…)
└── config/config.php    # Cấu hình DB

barber-admin/            # Entry point admin + CSS/JS admin
db_barber.sql            # Database hoàn chỉnh (import 1 lần)
docs/                    # Tài liệu luồng code (tiếng Việt)
```

**URL routing**

- Công khai: `index.php`, `index.php?url=appointment`
- Admin: `barber-admin/index.php?route=dashboard`, `?route=pos`, …

---

## Chức năng — Trang khách

| Chức năng | Mô tả |
|-----------|--------|
| Trang chủ | Giới thiệu tiệm, dịch vụ |
| Đặt lịch online | Chọn dịch vụ, thợ, ngày giờ theo lịch làm việc |
| Lịch trống | Khung giờ dựa trên lịch nhân viên đã cấu hình |
| Liên hệ | Gửi tin nhắn qua form |

---

## Chức năng — Quản trị

### Vận hành hàng ngày

| Module | Route | Mô tả |
|--------|-------|--------|
| Bản tin | `dashboard` | Tổng quan khách, dịch vụ, nhân viên, lịch hẹn |
| Thu ngân (POS) | `pos` | Bán dịch vụ & sản phẩm, thanh toán, in hóa đơn |
| Lịch hẹn | `booking` | Xem, tạo, cập nhật trạng thái lịch |
| Tạo lịch | `booking/create` | Đặt lịch tại quầy |
| Khách hàng (CRM) | `crm` | Danh sách, tìm kiếm, thêm/sửa/xóa khách |
| Kho hàng | `inventory` | Sản phẩm, tồn kho, thêm sản phẩm |

### Dịch vụ

| Module | Route | Mô tả |
|--------|-------|--------|
| Danh mục dịch vụ | `service-categories` | CRUD danh mục |
| Danh sách dịch vụ | `services` | CRUD dịch vụ, giá VND |

### Nhân viên

| Module | Route | Mô tả |
|--------|-------|--------|
| Chấm công | `settings/attendance` | Vào ca / ra ca (mỗi ngày một lần), lịch sử |
| Danh sách nhân viên | `employees` | Thêm, sửa, chức vụ |
| Quản lý lương | `hr` | Lương, nghỉ phép, thưởng/phạt, thanh toán |
| Chi tiết lương | `hr/detail` | Hoa hồng, ca làm, nghỉ, thanh toán từng NV |

### Cài đặt

| Module | Route | Mô tả |
|--------|-------|--------|
| Lịch làm việc | `employees-schedule` | Giờ làm theo ngày (dùng cho đặt lịch online) |
| Tùy chỉnh hoa hồng | `settings/commission` | % theo danh mục, từng dịch vụ, từng sản phẩm |
| Khuyến mại | `settings/promotions` | Giảm giá theo số lần dùng dịch vụ (VD: lần 5 giảm 50%) |

### Báo cáo

| Module | Route | Mô tả |
|--------|-------|--------|
| Thống kê | `reports` | Doanh thu, thu chi, hoa hồng, lọc theo thời gian |
| Chi tiết hóa đơn | `reports/order` | Xem từng đơn thanh toán |

---

## Tính năng nổi bật

- **Thu ngân POS**: dịch vụ + sản phẩm, nhiều hình thức thanh toán
- **CRM**: quản lý khách, sinh nhật
- **Lương & HR**: chấm công, nghỉ có/không phép, thưởng phạt, tự ghi chi phí lương vào thu chi
- **Hoa hồng**: cấu hình chi tiết theo danh mục / dịch vụ / sản phẩm; tính từ dòng hóa đơn
- **Khuyến mại**: quy tắc theo lần sử dụng dịch vụ, reset chu kỳ
- **Báo cáo**: doanh thu, chi phí, dòng tiền, hoa hồng nhân viên
- **Tiền tệ VND**: `format_vnd()`, giá dịch vụ `DECIMAL(12,2)`

---

