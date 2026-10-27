# Tổng hợp dự án Quản lý Sinh viên

> File này gộp nội dung của 4 tài liệu cũ (`TAI_LIEU_TONG_HOP_QLSV.md`, `SPRINT_CHI_TIET.md`,
> `USER_STORY_PRODUCT_BACKLOG.md`, `DANH_SACH_TASK_SPRINT.csv`) thành một bản **ngắn – dễ đọc**.

## 1. Dự án làm gì?

Website quản lý sinh viên thay cho quản lý bằng Excel: quản lý khoa/ngành/lớp, hồ sơ sinh viên,
lớp học phần, nhập điểm – tính GPA, thống kê và báo cáo. Hệ thống có **4 vai trò**:
**Admin**, **Giáo vụ**, **Giảng viên**, **Sinh viên**.

## 2. Công nghệ

| Thành phần | Sử dụng |
|---|---|
| Backend | Laravel 13 + PHP 8.3 |
| Cơ sở dữ liệu | MySQL 8.4, database `qlsv` |
| Giao diện | Blade + Tailwind CSS 4 + Vite |
| Báo cáo | Xuất CSV (mở bằng Excel) + in/Lưu PDF bằng trình duyệt |

## 3. Mỗi vai trò làm được gì?

| Vai trò | Việc chính |
|---|---|
| **Admin** | Toàn quyền: tài khoản, danh mục, sinh viên, lớp học phần, điểm, báo cáo, thông báo |
| **Giáo vụ** | Danh mục, hồ sơ sinh viên, lớp học phần, ghi danh, hỗ trợ điểm, báo cáo, thông báo |
| **Giảng viên** | Xem lớp được phân công, nhập điểm và chốt điểm lớp mình dạy |
| **Sinh viên** | Xem bảng điểm, GPA của mình; nhận thông báo; xem dashboard cá nhân |

## 4. 5 nhóm chức năng (theo đề cương)

| # | Nhóm chức năng | Làm được gì |
|---|---|---|
| 1 | **Đăng nhập & phân quyền** | Đăng nhập/đăng xuất 4 vai trò, khoá tài khoản, quên mật khẩu thì Admin reset; mỗi vai trò chỉ thấy menu/trang của mình |
| 2 | **Khoa – Ngành – Lớp** | Thêm/sửa/xoá, tìm kiếm; không cho xoá khi còn dữ liệu con |
| 3 | **Lớp học phần** | Mở lớp từ Môn + Học kỳ + Giảng viên, ghi danh sinh viên (chặn trùng, chặn vượt sĩ số) |
| 4 | **Điểm** | Nhập điểm quá trình/thi → tự tính điểm TB, điểm chữ, hệ 4, đạt/không đạt; chốt điểm để không sửa được nữa |
| 5 | **Thống kê** | Dashboard + biểu đồ (SV theo khoa/ngành, đạt–rớt, điểm TB theo kỳ); xuất Excel, in bảng điểm |

> Ngoài 5 nhóm trên còn có phần **hỗ trợ**: quản lý sinh viên (hồ sơ, ảnh, import CSV), thông báo nội bộ.

## 5. Dữ liệu chính (12 bảng)

| Nhóm dữ liệu | Bảng |
|---|---|
| Tài khoản | `users` (phân biệt vai trò bằng cột `role`) |
| Danh mục | `faculties` (Khoa), `majors` (Ngành), `classes` (Lớp), `subjects` (Môn), `semesters` (Học kỳ) |
| Sinh viên | `students` |
| Lớp học phần | `courses` (lớp HP), `enrollments` (ghi danh) |
| Điểm | `grades` (1 ghi danh ↔ 1 bộ điểm) |
| Thông báo | `announcements`, `announcement_user` (ai đã đọc) |

**Quan hệ:** Khoa → Ngành → Lớp → Sinh viên; Môn + Học kỳ + Giảng viên → Lớp HP → Ghi danh → Điểm.

## 6. Quy tắc nghiệp vụ cần nhớ

- **Điểm TB** = Điểm quá trình × 0,4 + Điểm thi × 0,6 (làm tròn 2 chữ số). **Đạt** khi TB ≥ 4,0.
- **Quy đổi:** ≥ 8,5 → A (4,0) · ≥ 8,0 → B+ (3,5) · ≥ 7,0 → B (3,0) · ≥ 6,5 → C+ (2,5) · ≥ 5,5 → C (2,0) ·
  ≥ 5,0 → D+ (1,5) · ≥ 4,0 → D (1,0) · dưới 4,0 → F (0,0).
- **GPA** = Σ(điểm hệ 4 × số tín chỉ) / Σ số tín chỉ. Xếp loại: Xuất sắc ≥ 3,60 · Giỏi ≥ 3,20 · Khá ≥ 2,50 ·
  Trung bình ≥ 2,00 · Yếu dưới 2,00. Môn học lại lấy điểm cao nhất.
- **Chốt điểm** → khoá, không sửa được; chỉ Admin/Giáo vụ mới mở khoá.
- **Ghi danh**: không trùng sinh viên trong cùng lớp, không vượt sĩ số; chỉ rút được khi lớp chưa chốt điểm.
- **Xoá dữ liệu**: không xoá Khoa/Ngành/Lớp/Môn/Học kỳ khi còn dữ liệu con; xoá sinh viên là xoá mềm (có khôi phục).
- **Bảo mật**: mỗi người chỉ xem dữ liệu của mình (sinh viên không xem điểm bạn khác, giảng viên chỉ lớp mình dạy).

## 7. Kế hoạch 4 sprint

| Sprint | Thời gian | Nội dung | Kết quả |
|:--|:--|:--|:--|
| **SP1** | Tuần 1–2 | Đăng nhập, phân quyền, quản lý tài khoản, hồ sơ cá nhân | 4 vai trò vào đúng màn hình; sai quyền bị chặn (403) |
| **SP2** | Tuần 3–4 | Khoa/Ngành/Lớp, Môn/Học kỳ, Sinh viên, Lớp học phần, ghi danh | Tạo đủ dữ liệu nền và mở lớp, ghi danh được |
| **SP3** | Tuần 5–6 | Nhập điểm, tính điểm tự động, chốt điểm, sinh viên xem điểm/GPA | Nhập 8/9 → TB 8,60 (A); chốt xong không sửa được |
| **SP4** | Tuần 7–8 | Dashboard, biểu đồ, xuất CSV, in bảng điểm | Có số liệu thống kê và báo cáo xuất được |

> Chi tiết chia sprint: xem `PHAN_CHIA_SPRINT.md`.

## 8. Cài đặt & chạy

```bash
composer install
npm install && npm run build
copy .env.example .env        # macOS/Linux: cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan serve             # http://127.0.0.1:8000
```

**Tạo tài khoản admin đầu tiên** (dự án không có seeder nên database trống sau khi migrate):

```bash
php artisan tinker
>>> App\Models\User::create(['name' => 'Quản trị viên', 'email' => 'admin@qlsv.test', 'password' => 'Admin@123', 'role' => 'admin', 'trang_thai' => true]);
```

Sau đó đăng nhập bằng tài khoản này, vào **Quản lý tài khoản** để tạo Giáo vụ / Giảng viên / Sinh viên.
Toàn bộ dữ liệu mẫu (khoa, ngành, lớp, môn, sinh viên, lớp HP) nhập trực tiếp qua giao diện.

## 9. Kiểm tra trước khi demo

| Việc cần làm | Lệnh / cách kiểm tra |
|---|---|
| Code style | `vendor/bin/pint --test` |
| Route & phân quyền | `php artisan route:list` |
| Giao diện | `npm run build` rồi mở trình duyệt |
| Chức năng | Kiểm thử thủ công theo checklist: đăng nhập 4 vai trò, 403 khi sai quyền, CRUD danh mục, import CSV, ghi danh, nhập/chốt điểm, xuất CSV, in bảng điểm |
| Dữ liệu | Backup MySQL trước buổi demo |

> Dự án **không dùng seeder và không có bộ test tự động** — kiểm thử bằng tay theo checklist trên.

## 10. Rủi ro chính & cách xử lý

| Rủi ro | Cách xử lý |
|---|---|
| Công thức/thang điểm khác quy chế | Chốt sớm với giảng viên/giáo vụ; mọi trọng số nằm trong `config/diem.php` |
| Sửa điểm sau khi đã chốt | Dùng `grades.khoa_luc`, chặn ở controller, form chuyển chỉ đọc |
| Người dùng xem dữ liệu người khác | Middleware `role` + `CoursePolicy`; không nhận `student_id` từ URL |
| Quên build giao diện trước demo | Luôn chạy `npm run build` trong checklist |
| Mất dữ liệu | Backup DB trước các thao tác rủi ro |

## 11. Các file tài liệu

| File | Nội dung |
|---|---|
| `TONG_HOP_QLSV.md` | **File này** — tổng hợp ngắn gọn toàn dự án |
| `PHAN_CHIA_SPRINT.md` | Bảng chia 4 sprint (rất ngắn) |
| `README.md` | Hướng dẫn cài đặt, chạy, cấu trúc thư mục |

> 4 tài liệu cũ (`TAI_LIEU_TONG_HOP_QLSV.md`, `SPRINT_CHI_TIET.md`, `USER_STORY_PRODUCT_BACKLOG.md`,
> `DANH_SACH_TASK_SPRINT.csv`) đã được gộp vào file này để tránh trùng lặp và dễ đọc hơn.

