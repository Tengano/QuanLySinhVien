# 🎓 QuanLySinhVien — Hệ thống Quản lý Sinh viên

Website quản lý sinh viên xây dựng bằng **Laravel 13 + MySQL**, phân quyền theo vai trò
(**Admin / Giáo vụ / Giảng viên / Sinh viên**), thay thế việc quản lý thủ công bằng Excel;
hỗ trợ thống kê trực quan, import/export CSV và in báo cáo/PDF từ trình duyệt.

## 🧰 Công nghệ sử dụng

| Thành phần | Công nghệ |
|---|---|
| Backend | Laravel 13 (PHP 8.3), Eloquent ORM, mô hình MVC |
| Cơ sở dữ liệu | MySQL 8.4 — database `qlsv`, charset `utf8mb4` |
| Frontend | Blade Template + Tailwind CSS 4 + Vite 8 |
| Xác thực & phân quyền | Auth session (guard có sẵn) + middleware alias `role` |
| Kiểm thử | Không có bộ test tự động — kiểm thử thủ công theo checklist AC/UAT |
| Quy trình | Git + Agile/Scrum (GitHub Projects) |

## ✨ Tính năng (phạm vi MVP)

**Quản trị & danh mục**
- Đăng nhập/đăng xuất, phân quyền 4 vai trò, quản lý tài khoản (tạo/sửa/khoá/reset mật khẩu)
- Danh mục: Khoa, Ngành, Lớp, Môn học, Học kỳ

**Nghiệp vụ**
- Quản lý Sinh viên: CRUD, hồ sơ, import CSV (mở được bằng Excel), ảnh đại diện, tìm kiếm & lọc
- Lớp học phần: tạo lớp, phân công giảng viên, gán sinh viên
- Điểm: nhập điểm quá trình/thi, tự tính điểm TB – điểm chữ – hệ 4 – GPA; sinh viên tra cứu điểm của mình
- Dashboard thống kê (biểu đồ theo khoa/lớp, tỉ lệ đạt/rớt, điểm trung bình)
- Xuất danh sách sinh viên/bảng điểm ra CSV (Excel) và in/Lưu PDF từ trình duyệt
- Thông báo nội bộ gửi theo toàn trường/khoa/lớp/lớp HP/người dùng

> **Trạng thái hiện tại:** MVP đã hoàn thiện các sprint 0–6, build Tailwind thành công.
> Dự án **không dùng seeder và không có bộ test tự động**: dữ liệu demo nhập qua giao diện, kiểm thử theo checklist
> UAT trong `TAI_LIEU_TONG_HOP_QLSV.md`; chỉ còn kiểm tra code style bằng `vendor/bin/pint --test`.

## ✅ Yêu cầu hệ thống

- **PHP >= 8.3** (bật các extension: `mbstring, openssl, pdo_mysql, tokenizer, xml, ctype, json, bcmath, fileinfo, gd, zip`)
- **Composer >= 2.x**
- **MySQL >= 8.0** (khuyến nghị 8.4)
- **Node.js >= 18** & npm (build asset Vite)
- **Git**

## ⚙️ Cài đặt

```bash
# 1. Cài dependency PHP
composer install

# 2. Tạo file môi trường & khoá ứng dụng
copy .env.example .env        # macOS/Linux: cp .env.example .env
php artisan key:generate

# 3. Cấu hình kết nối MySQL trong .env
#    DB_CONNECTION=mysql
#    DB_DATABASE=qlsv
#    DB_USERNAME=root
#    DB_PASSWORD=

# 4. Tạo database rồi chạy migration
php artisan migrate

# 5. Tạo tài khoản quản trị đầu tiên (dự án không còn seeder demo)
php artisan tinker
>>> App\Models\User::create(['name' => 'Quản trị viên', 'email' => 'admin@qlsv.test', 'password' => 'Admin@123', 'role' => 'admin', 'trang_thai' => true]);
>>> exit

# 6. Cài & build asset frontend
npm install
npm run build

# 7. Tạo symlink cho ảnh/upload
php artisan storage:link
```

Hoặc chạy nhanh toàn bộ bằng script có sẵn: `composer setup` (script chỉ chạy `migrate --force`, **không** tạo
dữ liệu/tài khoản — vẫn cần làm bước 5 để có tài khoản đăng nhập).

## ▶️ Chạy dự án

```bash
php artisan serve          # http://127.0.0.1:8000
npm run dev                # Vite dev server (hot reload)
```

Nếu dùng **Laragon**: mở Laragon → *Start All*, sau đó truy cập domain ảo (ví dụ `http://qlsv.test`).

## 🔑 Tài khoản đầu tiên

Dự án **không còn seeder** (`database/seeders` đã được bỏ) nên sau `php artisan migrate` database trống và
chưa có tài khoản nào để đăng nhập (hệ thống không có route đăng ký). Tạo tài khoản quản trị đầu tiên:

```bash
php artisan tinker
>>> App\Models\User::create(['name' => 'Quản trị viên', 'email' => 'admin@qlsv.test', 'password' => 'Admin@123', 'role' => 'admin', 'trang_thai' => true]);
>>> exit
```

Sau đó đăng nhập bằng tài khoản này và vào **Quản lý tài khoản** (`/users`) để tạo tiếp tài khoản
Giáo vụ / Giảng viên / Sinh viên. Với sinh viên, cần liên kết hồ sơ bằng `students.user_id`.

> Dữ liệu mẫu (khoa/ngành/lớp, môn học, sinh viên, lớp học phần, điểm) được nhập trực tiếp qua giao diện sau khi
> đăng nhập bằng tài khoản admin ở trên.

## ✅ Kiểm tra chất lượng

Dự án không có bộ test tự động (`tests/`, `phpunit.xml`, `database/factories` đã được bỏ). Kiểm tra trước khi
bàn giao/demo:

```bash
vendor/bin/pint --test     # kiểm tra code style (tự sửa: vendor/bin/pint)
php artisan route:list     # rà route & middleware role
npm run build              # build Tailwind/Vite
```

Kiểm thử chức năng thực hiện **thủ công theo checklist UAT** trong `TAI_LIEU_TONG_HOP_QLSV.md` (mục 10.5):
đăng nhập 4 vai trò, phân quyền 403, CRUD danh mục, import CSV, ghi danh, nhập/chốt điểm, báo cáo, thông báo.

## 📂 Cấu trúc thư mục

```text
app/
├── Enums/            Role, GioiTinh, TrangThaiSinhVien, TrangThaiLopHocPhan, ...
├── Http/
│   ├── Controllers/  Auth, MasterData, Student, Course, Enrollment, Grade, Announcement, Inbox, Portal, Report
│   └── Middleware/   EnsureUserHasRole.php
├── Models/           User, Faculty, Major, SchoolClass, Student, Subject, Semester, Course, Enrollment, Grade, Announcement
├── Policies/         CoursePolicy
├── Providers/        AppServiceProvider (rate limit đăng nhập)
└── Services/         DiemService, GpaService, ThongKeService
config/               diem.php
database/             migrations
resources/views/      layouts, partials, auth, dashboard, admin, students, courses, announcements, inbox, portal, reports
routes/web.php
```

## 🗺️ Lộ trình phát triển (đã hoàn thành MVP)

| Sprint | Nội dung | Trạng thái |
|---|---|---|
| **Sprint 0** | Cài đặt môi trường, layout nền, i18n tiếng Việt, enum & config nghiệp vụ | ✅ |
| **Sprint 1** | Đăng nhập, phân quyền theo vai trò, quản lý tài khoản, hồ sơ cá nhân | ✅ |
| **Sprint 2** | Danh mục: Khoa, Ngành, Lớp, Môn học, Học kỳ | ✅ |
| **Sprint 3** | Quản lý Sinh viên: CRUD, import CSV, ảnh, tìm kiếm/lọc | ✅ |
| **Sprint 4** | Lớp học phần, ghi danh, nhập điểm, tính điểm TB/GPA tự động | ✅ |
| **Sprint 5** | Sinh viên tra cứu điểm, dashboard thống kê | ✅ |
| **Sprint 6** | Báo cáo CSV/in PDF, thông báo, kiểm thử, tài liệu | ✅ |

## 📚 Tài liệu dự án

- [`TONG_HOP_QLSV.md`](TONG_HOP_QLSV.md) — **tổng hợp toàn dự án** (ngắn gọn, 1 file duy nhất)
- [`PHAN_CHIA_SPRINT.md`](PHAN_CHIA_SPRINT.md) — **bảng chia 4 sprint** (ngắn gọn, dễ đọc khi báo cáo)
- [`KE_HOACH_4_SPRINT.md`](KE_HOACH_4_SPRINT.md) — kế hoạch 4 sprint chi tiết (task, AC, rủi ro)
- [`TAI_LIEU_TONG_HOP_QLSV.md`](TAI_LIEU_TONG_HOP_QLSV.md) — **tài liệu hợp nhất** (khuyến nghị đọc trước)
- [`AGILE_QLSV.md`](AGILE_QLSV.md) — kế hoạch Agile tổng thể
- [`SPRINT_CHI_TIET.md`](SPRINT_CHI_TIET.md) — đặc tả chi tiết từng sprint (task, tiêu chí nghiệm thu, demo)
- [`DANH_SACH_TASK_SPRINT.csv`](DANH_SACH_TASK_SPRINT.csv) — danh sách task dạng bảng
- [`USER_STORY_PRODUCT_BACKLOG.md`](USER_STORY_PRODUCT_BACKLOG.md) — User Story, Product Backlog và kế hoạch Sprint

## 📄 Giấy phép

Dự án phát hành theo giấy phép [MIT](https://opensource.org/licenses/MIT), xây dựng trên
[Laravel framework](https://laravel.com) của Taylor Otwell.
