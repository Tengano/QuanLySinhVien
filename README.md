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

# 5. Cài & build asset frontend
npm install
npm run build

# 6. Tạo symlink cho ảnh/upload
php artisan storage:link
```

## ▶️ Chạy dự án

```bash
php artisan serve          # http://127.0.0.1:8000
npm run dev                # Vite dev server (hot reload)
```

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
