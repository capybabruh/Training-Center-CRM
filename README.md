# Training Center CRM — Lab06 Final

Mini CRM quản lý **Lead tư vấn** (Module A) và **Order học phí** (Module B), xây dựng bằng PHP thuần theo kiến trúc MVC 3 tầng:

```
Browser → public/index.php → Router → Controller → Service → Repository → PDO → MySQL → View/Redirect
```

---

## 1. Yêu cầu môi trường

| Phần mềm | Phiên bản tối thiểu | Kiểm tra bằng lệnh |
|---|---|---|
| PHP | 8.1+ (có extension `pdo_mysql`) | `php -v` |
| MySQL / MariaDB | 5.7+ / 10.3+ | `mysql --version` |
| Git | bất kỳ | `git --version` |

---

## 2. Cài đặt từ đầu

### Bước 1 — Giải nén / clone project

```bash
cd /training-center-crm
```

### Bước 2 — Tạo database

Dùng **PowerShell** (Windows), do PowerShell không hỗ trợ `<` như CMD/bash, phải dùng `Get-Content`:

```powershell
Get-Content database\schema.sql | mysql -u root -p
Get-Content database\seed.sql | mysql -u root -p
```

Nếu dùng **CMD** hoặc **macOS/Linux**, dùng cú pháp `<` bình thường:

```bash
mysql -u root -p < database/schema.sql
mysql -u root -p < database/seed.sql
```

### Bước 3 — Sinh password hash thật cho tài khoản demo

> ⚠️ Hash trong `seed.sql` chỉ là **placeholder minh họa**, chưa chắc đúng định dạng bcrypt thật. Bắt buộc phải làm bước này trước khi đăng nhập được.

```bash
php -r "echo password_hash('password', PASSWORD_DEFAULT), PHP_EOL;"
```

Copy chuỗi kết quả (dạng `$2y$10$....`), sau đó chạy trong MySQL (Workbench hoặc CLI):

```sql
USE web_php_lab06_crm;
UPDATE users
SET password_hash = 'PASTE_HASH_VUA_COPY_VAO_DAY'
WHERE email IN ('admin@crm.local', 'an@crm.local');
```

### Bước 4 — Cấu hình kết nối DB

M�� `config/database.php`, sửa `username`/`password` cho khớp MySQL thật trên máy bạn:

```php
<?php
return [
    'host'     => 'localhost',
    'database' => 'web_php_lab06_crm',
    'username' => 'root',
    'password' => 'MẬT_KHẨU_MYSQL_CỦA_BẠN',
    'charset'  => 'utf8mb4',
];
```

### Bước 5 — Cấu hình môi trường app

M�� `config/app.php`:

```php
<?php
return [
    'name'            => 'Training Center CRM',
    'debug'           => false,   // true khi dev để hiện lỗi chi tiết, false khi production
    'session_timeout' => 1800,    // giây — 1800 = 30 phút
    'per_page'        => 10,
    'base_url'        => 'http://localhost:8000',
];
```

### Bước 6 — Chạy server

```bash
php -S localhost:8000 -t public
```

M�� trình duyệt: **http://localhost:8000** → tự động chuyển tới `/login`.

---

## 3. Tài khoản demo

| Vai trò | Email | Mật khẩu | Quyền |
|---|---|---|---|
| Admin | `admin@crm.local` | `password` | Tạo, sửa, **xóa** lead/order |
| Staff | `an@crm.local` | `password` | Chỉ tạo, sửa — **không xóa được** |

---

## 4. (Tuỳ chọn) Seed dữ liệu lớn để test pagination/EXPLAIN

```bash
php database/seed_data.php
```

Sinh thêm 200 leads + 200 orders (mỗi order có `paid_amount > 0` sẽ kèm theo 1 payment record, tạo qua transaction).

---

## 5. Cấu trúc thư mục

```
training-center-crm
├── app/
│   ├── Controllers/   HomeController, AuthController, DashboardController,
│   │                  LeadController, OrderController, PublicLeadController, HealthController
│   ├── Services/      AuthService, LeadService, OrderService  (validate + business logic)
│   ├── Repositories/  UserRepository, LeadRepository, OrderRepository  (toàn bộ SQL)
│   ├── Core/          Database, Router, helpers.php, DuplicateRecordException
│   └── Views/         layouts/, partials/, auth/, dashboard/, leads/, orders/, public/, errors/
├── config/            app.php, database.php
├── database/          schema.sql, seed.sql, seed_data.php
├── public/            index.php (Front Controller), assets/style.css
└── storage/logs/      app.log
```

---

## 6. Bảng route đầy đủ

| Method | URL | Ghi chú |
|---|---|---|
| GET | `/` | Redirect theo trạng thái đăng nhập |
| GET | `/health` | JSON kiểm tra kết nối DB |
| GET / POST | `/login` | Hiện form / xử lý đăng nhập |
| POST | `/logout` | Đăng xuất |
| GET | `/dashboard` | Thống kê tổng quan (cần login) |
| GET / POST | `/public-leads/create`, `/public-leads` | Form đăng ký công khai (honeypot + rate limit) |
| GET | `/leads` | List + search + filter status + date range + sort |
| GET / POST | `/leads/create`, `/leads/store` | Tạo lead |
| GET / POST | `/leads/edit`, `/leads/update` | Sửa lead |
| POST | `/leads/delete` | Xóa (soft delete, **chỉ admin**) |
| GET | `/orders` | List + search + filter + sort |
| GET / POST | `/orders/create`, `/orders/store` | Tạo order (kèm payment nếu có đặt cọc, qua transaction) |
| GET / POST | `/orders/edit`, `/orders/update` | Sửa order |
| POST | `/orders/delete` | Xóa (soft delete, **chỉ admin**) |
| ANY | URL lạ | 404 |
| — | Sai method | 405 |

---

## 7. Test nhanh bằng curl

```bash
# Health check
curl -i http://localhost:8000/health

# 404
curl -i http://localhost:8000/khong-ton-tai

# 405 — /leads/delete chỉ nhận POST
curl -i http://localhost:8000/leads/delete?id=1

# 405 — /health chỉ nhận GET
curl -i -X POST http://localhost:8000/health

# Chưa login vẫn cố vào trang cần login -> redirect /login
curl -i http://localhost:8000/dashboard

# Sort injection -> phải fallback về mặc định, không lỗi
curl -i "http://localhost:8000/leads?sort=id+DESC%3B+DROP+TABLE+leads%3B--"

# Page âm / quá lớn -> tự chuẩn hóa
curl -i "http://localhost:8000/leads?page=-5"
curl -i "http://localhost:8000/leads?page=9999"
```

---

## 8. Tính năng đã hoàn chỉnh

- PDO chuẩn: `charset=utf8mb4`, `ERRMODE_EXCEPTION`, `FETCH_ASSOC`, `EMULATE_PREPARES=false`
- MVC 3 tầng: Controller mỏng → Service (validate + business logic) → Repository (toàn bộ SQL)
- Session login: `session_regenerate_id()` chống fixation, session timeout tự động
- PRG Pattern: mọi POST thành công đều redirect
- Sort/direction qua whitelist, không lấy thẳng từ `$_GET`
- Bắt lỗi trùng unique key (`email`, `order_code`), báo lỗi thân thiện, giữ lại dữ liệu đã nhập
- Soft delete (`deleted_at`), CSRF token mọi form POST
- Role permission: admin xóa được, staff chỉ tạo/sửa
- Transaction khi tạo order kèm payment record
- Filter theo status + date range, dashboard thống kê
- Form công khai có honeypot + rate limit chống spam
- Trang lỗi 404 / 405 / 403 / 500 riêng biệt


## 9. Lưu ý bảo mật khi triển khai thật

- Đổi `config/app.php` → `'debug' => false` trước khi deploy (mặc định đã là `false`)
- Không commit `config/database.php` chứa password thật lên Git công khai
- Sinh lại password hash thật cho `users` (xem Bước 3), không dùng hash mẫu trong `seed.sql`
- Bật HTTPS để cookie `secure` hoạt động đúng (đã cấu hình sẵn trong `public/index.php`)
