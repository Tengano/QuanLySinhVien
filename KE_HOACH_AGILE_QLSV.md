# Kế hoạch Agile — Website Quản lý Sinh viên

> **Stack:** Laravel (PHP) + MySQL · **Mô hình:** Agile/Scrum, sprint 1–2 tuần · **Cập nhật:** 12/10/2026
> **Chủ đề:** "Xây dựng website quản lý sinh viên"

---

## 1. Mục tiêu & Phạm vi

**Mục tiêu:** Xây dựng website quản lý sinh viên dùng Laravel + MySQL, phân quyền theo vai trò, thay thế quản lý thủ công/Excel, có thống kê và xuất báo cáo.

**Trong phạm vi (MVP):**
- Đăng nhập & phân quyền (Admin / Giáo vụ / Giảng viên / Sinh viên)
- Quản lý danh mục: Khoa, Ngành, Lớp
- Quản lý Sinh viên (CRUD + hồ sơ + import Excel + ảnh đại diện)
- Quản lý Môn học & Học kỳ
- Lớp học phần & phân công giảng viên
- Nhập điểm, tính điểm TB/GPA, bảng điểm, tra cứu điểm cho sinh viên
- Dashboard thống kê + xuất báo cáo Excel/PDF

**Ngoài phạm vi (giai đoạn sau):** Thanh toán học phí online, đăng ký tín chỉ nâng cao, điểm danh QR, ứng dụng mobile.

---

## 2. Kiểm tra môi trường (đã kiểm tra thực tế trên máy bạn)

| Thành phần | Máy bạn | Yêu cầu | Kết luận |
|---|---|---|---|
| PHP | 8.3.30 (ZTS, x64, Laragon) | PHP ≥ 8.3 | ✅ Đạt |
| Composer | 2.10.2 | ≥ 2.x | ✅ Đạt |
| MySQL | 8.4.3 (Laragon) | ≥ 8.0 | ✅ Đạt |
| Node / npm | 24.14.1 / 11.11.0 | Node ≥ 18 | ✅ Đạt |
| Git | 2.53.0 | — | ✅ Đạt |
| Hệ điều hành | Windows 11 | — | ✅ |
| memory_limit / post_max_size | 512M / 2G | ≥ 128M | ✅ Đạt |

**Extension PHP đã có (đủ cho Laravel):** `ctype, curl, dom, fileinfo, filter, hash, mbstring, openssl, pcre, PDO, pdo_mysql, session, tokenizer, xml` ✅
**Có thêm (dùng cho tính năng nâng cao):** `bcmath, gd, intl, zip, sodium, exif` → hỗ trợ tốt Excel, PDF, xử lý ảnh.

### Kết luận & khuyến nghị phiên bản
- **Không cần cài thêm extension PHP nào.** Máy đã đủ chạy Laravel.
- **Khuyến nghị: Laravel 13.x** — phát hành Q1/2026, hỗ trợ PHP 8.3–8.5, bug-fix đến Q3/2027, security đến Q1/2028.
- Phương án dự phòng: **Laravel 12.x** (chạy tốt với PHP 8.3, nhưng chỉ còn security-fix đến 24/02/2027). **Tránh Laravel ≤ 11** (đã hết bug-fix).
- Cần làm trước: mở **Laragon → Start All** (để chạy Apache/Nginx + MySQL). Tùy chọn: thêm `mysql` vào PATH để dùng CLI.
- **Lưu ý MySQL 8.4:** plugin xác thực mặc định là `caching_sha2_password` (`mysql_native_password` đã bị tắt) — PHP 8.3 (mysqlnd) hỗ trợ tốt, tạo user DB cứ để mặc định.

### Lệnh khởi tạo dự án
```bat
:: Cách 1: đặt trong thư mục web Laragon (có domain ảo qlsv.test)
cd K:\Apps\Laragon\www && composer create-project laravel/laravel qlsv

:: Cách 2: đặt trong thư mục làm việc hiện tại, chạy bằng artisan serve
cd K:\CNPM && composer create-project laravel/laravel qlsv
cd qlsv && php artisan serve   :: http://127.0.0.1:8000

:: Tạo DB rồi cấu hình .env: DB_DATABASE=qlsv, DB_USERNAME=root, DB_PASSWORD=
php artisan migrate --seed
```

---

## 3. Kiến trúc & Công nghệ

- **Mô hình:** MVC (Model – View – Controller), server-side rendering.
- **Backend:** Laravel 13 (PHP 8.3), Eloquent ORM, Migration + Seeder + Factory.
- **Frontend:** Blade Template + Bootstrap 5 + Vite, JS thuần / Alpine.js.
- **CSDL:** MySQL 8.4, InnoDB, charset `utf8mb4`.
- **Auth & phân quyền:** Session auth + middleware role (Spatie Permission).
- **Cấu trúc thư mục chính:** `app/Models`, `app/Http/Controllers`, `app/Http/Requests` (validate), `database/migrations`, `database/seeders`, `resources/views`, `routes/web.php`.

```
routes/web.php → Controller → (Request validate) → Model/Eloquent → MySQL
                              ↘ Blade View (+ Bootstrap/Vite) → Browser
```

---

## 4. Thư viện cần cài

> **Không cần cài thêm extension PHP.** Chỉ cần cài package Composer (backend) và npm (frontend).

**Composer (backend):**

| Gói | Lệnh | Mục đích |
|---|---|---|
| laravel/framework | (có sẵn khi create-project) | Framework |
| laravel/breeze | `composer require laravel/breeze --dev` rồi `php artisan breeze:install blade` | Scaffold đăng nhập/đăng ký nhanh |
| spatie/laravel-permission | `composer require spatie/laravel-permission` | Phân quyền role/permission |
| maatwebsite/excel | `composer require maatwebsite/excel` | Import/Export Excel (SV, điểm) |
| barryvdh/laravel-dompdf | `composer require barryvdh/laravel-dompdf` | Xuất PDF (bảng điểm) |
| intervention/image | `composer require intervention/image` | Xử lý ảnh đại diện (cần ext gd ✔) |
| barryvdh/laravel-debugbar (dev) | `composer require --dev barryvdh/laravel-debugbar` | Debug |
| laravel/pint (dev) | (có sẵn) | Chuẩn hoá code style |

**npm (frontend):**

| Gói | Mục đích |
|---|---|
| bootstrap hoặc tailwindcss | UI framework |
| chart.js | Biểu đồ thống kê |
| sweetalert2 | Thông báo/confirm đẹp |
| select2 / flatpickr | Combobox & chọn ngày |
| vite | Build asset (có sẵn) |

---

## 5. Mô hình dữ liệu (bảng chính)

| Bảng | Trường quan trọng | Ghi chú |
|---|---|---|
| `users` | id, name, email, password, role | Tài khoản đăng nhập |
| `roles`, `permissions`, `model_has_roles` | — | Spatie Permission |
| `faculties` | id, ma_khoa, ten_khoa | Khoa |
| `majors` | id, faculty_id, ma_nganh, ten_nganh | Ngành (thuộc Khoa) |
| `classes` | id, major_id, ma_lop, ten_lop, khoa_hoc, gvcn_id | Lớp hành chính |
| `students` | id, user_id, ma_sv, ho_ten, ngay_sinh, gioi_tinh, lop_id, email, sdt, dia_chi, anh, trang_thai | Hồ sơ sinh viên |
| `subjects` | id, ma_mh, ten_mh, so_tin_chi, faculty_id | Môn học |
| `semesters` | id, ten_hoc_ky, nam_hoc, ngay_bat_dau, ngay_ket_thuc | Học kỳ |
| `courses` | id, subject_id, semester_id, giang_vien_id, ma_lhp | Lớp học phần |
| `enrollments` | id, student_id, course_id | Đăng ký học |
| `grades` | id, enrollment_id, diem_qua_trinh, diem_thi, diem_tb, diem_chu, diem_he4 | Điểm |
| `notifications` | id, user_id, tieu_de, noi_dung, da_doc | Thông báo |

**Quan hệ chính:** Faculty 1–n Major 1–n Class 1–n Student; Subject 1–n Course; Student n–n Course qua Enrollment; Enrollment 1–1 Grade.

---

## 6. Product Backlog (Epic → User Story)

| # | Epic | User Story tiêu biểu | Ưu tiên |
|---|---|---|---|
| E1 | Xác thực & phân quyền | Đăng nhập/đăng xuất; quản lý tài khoản; gán vai trò để chặn truy cập trái phép | Cao |
| E2 | Danh mục | CRUD Khoa / Ngành / Lớp để chuẩn hoá dữ liệu | Cao |
| E3 | Quản lý Sinh viên | Thêm/sửa/xoá/mềm SV; import Excel; upload ảnh; tìm kiếm & lọc theo lớp/khoa | Cao |
| E4 | Môn học & Học kỳ | CRUD môn học (tín chỉ), học kỳ/năm học | Cao |
| E5 | Lớp học phần | Tạo lớp HP, phân công giảng viên, gán SV | Trung bình |
| E6 | Điểm | Giảng viên nhập điểm QT/thi; hệ thống tính điểm TB, điểm chữ, hệ 4, GPA; SV tra cứu điểm của mình | Cao |
| E7 | Dashboard & Thống kê | Biểu đồ số SV theo khoa/lớp, tỉ lệ đạt/rớt, số môn, điểm trung bình | Trung bình |
| E8 | Báo cáo | Xuất danh sách SV / bảng điểm ra Excel & PDF | Trung bình |
| E9 | Thông báo | Admin/giáo vụ gửi thông báo; SV xem thông báo | Thấp |

---

## 7. Kế hoạch Sprint (6 sprint × 2 tuần ≈ 12 tuần)

| Sprint | Thời lượng | Nội dung | Kết quả bàn giao (demo) |
|---|---|---|---|
| **Sprint 0** | 1 tuần | Setup Laragon, tạo project Laravel, Git, cấu hình `.env`, tạo DB, cài package, layout nền | App chạy được ở localhost |
| **Sprint 1** | 2 tuần | Auth + phân quyền (role/middleware) + layout admin | Đăng nhập 4 vai trò, chặn truy cập |
| **Sprint 2** | 2 tuần | Danh mục: Khoa, Ngành, Lớp, Môn học, Học kỳ | CRUD danh mục hoàn chỉnh |
| **Sprint 3** | 2 tuần | Quản lý Sinh viên: CRUD, import Excel, ảnh, tìm kiếm/lọc | Danh sách SV + import hoạt động |
| **Sprint 4** | 2 tuần | Lớp học phần + nhập điểm + tính điểm TB/GPA tự động | Nhập & tính điểm chuẩn |
| **Sprint 5** | 2 tuần | Tra cứu điểm cho SV + Dashboard thống kê (biểu đồ) | SV xem điểm, dashboard trực quan |
| **Sprint 6** | 2 tuần | Xuất báo cáo Excel/PDF + thông báo + kiểm thử + tối ưu + viết tài liệu | Bản hoàn thiện, sẵn sàng bảo vệ |

**Ghi chú:** Nếu làm 1 mình, có thể rút còn **4 sprint × 1 tuần**; nếu E7/E8/E9 không kịp thì hạ xuống mức "Nên có" (Should-have).

---

## 8. Quy trình Agile (Scrum rút gọn)

- **Vai trò:** Product Owner (người đặt yêu cầu), Scrum Master (điều phối), Dev Team (lập trình + test).
- **Artifacts:** Product Backlog → Sprint Backlog → Increment (phần mềm chạy được).
- **Nghi thức:** Sprint Planning (đầu sprint) · Daily Stand-up 15' (hôm trước/…/hôm nay/vướng mắc) · Sprint Review (demo) · Retrospective (cải tiến).
- **Công cụ quản lý:** GitHub Projects / Trello / Jira (kanban: To Do → In Progress → Review → Done).

**Definition of Done (DoD) — một task chỉ "Done" khi:**
- Chạy đúng chức năng, đã kiểm thử (thủ công + `php artisan test`).
- `php artisan migrate` chạy sạch; có Seeder dữ liệu mẫu.
- Có validate input (Form Request), chống SQL Injection/XSS/CSRF.
- Giao diện responsive (Bootstrap), hiển thị tiếng Việt, không lỗi console.
- Đã review code (Pint) và cập nhật trạng thái trên backlog.

---

## 9. Rủi ro & Giảm thiểu

| Rủi ro | Mức | Giảm thiểu |
|---|---|---|
| Phình phạm vi (scope creep) | Cao | Bám sát MVP, tính năng mới đưa vào backlog giai đoạn sau |
| Trễ tiến độ | Cao | Chia sprint nhỏ, mỗi sprint có demo chạy được |
| Lỗi import Excel (định dạng) | TB | Cung cấp file mẫu, validate từng dòng, báo lỗi rõ ràng |
| Bảo mật (SQLi/XSS/CSRF) | TB | Dùng Eloquent/validate, escape Blade, token CSRF mặc định |
| Xung đột phiên bản MySQL 8.4 | Thấp | Dùng `caching_sha2_password`, build="mysql", collation utf8mb4 |
| Mất dữ liệu khi phát triển | TB | Seed dữ liệu mẫu + backup DB định kỳ |

---

## 10. Bước tiếp theo (Checklist)

- [ ] Mở Laragon → **Start All** (Apache/Nginx + MySQL)
- [ ] `composer create-project laravel/laravel qlsv` → chạy `php artisan serve`
- [ ] Tạo DB `qlsv` (HeidiSQL/phpMyAdmin) và cấu hình `.env`
- [ ] Cài package: Breeze, Spatie Permission, Laravel Excel, DomPDF
- [ ] `git init` + commit khung dự án
- [ ] Hoàn thành Sprint 0 → bắt đầu Sprint 1

