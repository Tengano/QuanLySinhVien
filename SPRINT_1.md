# Sprint 1

| Loại | Số file | File |
|---|---|---|
| Controller | 3 | `DangNhapController`, `NguoiDungController`, `HoSoCaNhanController` |
| Middleware | 1 | `KiemTraVaiTro` |
| Enum | 2 | `VaiTro`, `HasLabel` (trait) |
| Model | 1 | `NguoiDung` |
| Provider | 1 | `AppServiceProvider` |
| Bootstrap | 1 | `bootstrap/app.php` |
| Config | 3 | `auth.php`, `qlsv.php`, `filesystems.php` |
| Route | 1 | `routes/web.php` |
| View (Blade) | 9 | 2 layout (`chinh`, `khach`) + 3 partial (`thanh-ben`, `thanh-tren`, `thong-bao`) + 1 login + 2 quản trị + 1 hồ sơ |
| Migration | 3 | `create_users_table`, `add_profile_and_role`, `doi_ten_bang_va_cot` |
| Môi trường | 2 | `.env`, `.env.example` |

---

## A. NHÓM CỐT LÕI SP1 (27 file)

### A1. Đăng nhập / Đăng xuất — 5 file

| File | Vai trò trong SP1 | Bằng chứng |
|---|---|---|
| `app/Http/Controllers/Auth/DangNhapController.php` | `create()` hiện form; `store()` xác thực, chặn tài khoản bị khoá, ghi `dang_nhap_cuoi_luc`; `destroy()` đăng xuất | dòng 14–17; 30–52 (kiểm tra `trang_thai` ở 40–48); 55–63 |
| `resources/views/xac-thuc/dang-nhap.blade.php` | Form email/mật khẩu + “Ghi nhớ đăng nhập” | dòng 20–45 (`route('login.store')`, `name="remember"`) |
| `resources/views/bo-cuc/khach.blade.php` | Layout guest chỉ dùng cho trang đăng nhập | dòng 7–11 (`@vite`/CDN), `@yield('content')` |
| `app/Providers/AppServiceProvider.php` | Rate limit đăng nhập **5 lần/phút** (`throttle:login`) | dòng 20–24 (`RateLimiter::for('login')`) |
| `routes/web.php` | `/` redirect; `GET/POST /login`; `POST /logout` | dòng 22–24; 26–29 (`throttle:login`); 32 |

### A2. Phân quyền — 3 file

| File | Vai trò trong SP1 | Bằng chứng |
|---|---|---|
| `app/Http/Middleware/KiemTraVaiTro.php` | Chặn **403** khi vai trò không thuộc danh sách cho phép (VD: sinh viên mở `/users`) | dòng 15–19 (`abort_if(..., 403)`) |
| `app/Enums/VaiTro.php` | 4 vai trò `admin / giao_vu / giang_vien / sinh_vien` + nhãn tiếng Việt | dòng 11–14 (cases); 16–24 (labels) |
| `app/Enums/Concerns/HasLabel.php` | `options()` sinh danh sách vai trò cho select/lọc | dòng 20–29 |

### A3. Layout & menu theo vai trò — 4 file

| File | Vai trò trong SP1 | Bằng chứng |
|---|---|---|
| `resources/views/bo-cuc/chinh.blade.php` | Layout chính: nạp Vite (fallback CDN), sidebar + topbar + flash | dòng 8–12; 17–23 |
| `resources/views/thanh-phan/thanh-ben.blade.php` | **Menu thay đổi theo vai trò** (Admin thấy “Người dùng”; Giảng viên/Sinh viên thấy menu riêng) | dòng 24–28 (`ADMIN`); 30–41; 42–45; 46–50 |
| `resources/views/thanh-phan/thanh-tren.blade.php` | Hiển thị họ tên + nhãn vai trò; nút **Hồ sơ**; nút **Đăng xuất** | dòng 20–25; 27–30 |
| `resources/views/thanh-phan/thong-bao.blade.php` | Flash `success`/`error` + lỗi validate | dòng 1–21 |

### A4. Quản lý tài khoản — 3 file

| File | Vai trò trong SP1 | Bằng chứng |
|---|---|---|
| `app/Http/Controllers/NguoiDungController.php` | CRUD tài khoản, **reset mật khẩu**, **khoá/mở khoá**, chặn tự xoá / tự khoá / tự đổi vai trò | 105–117 (reset); 119–130 (toggle); 74–80 (self-guard); 96–98 (chặn xoá khi đã liên kết sinh viên) |
| `resources/views/quan-tri/nguoi-dung/danh-sach.blade.php` | Danh sách + tìm kiếm + lọc vai trò/trạng thái + nút Reset / Khoá / Xoá | 24–26 (lọc vai trò); 60–62 (3 nút thao tác) |
| `resources/views/quan-tri/nguoi-dung/bieu-mau.blade.php` | Form tạo/sửa tài khoản (họ tên, email, vai trò, mật khẩu, trạng thái) | 23–27 (chọn vai trò); 43–46 (trạng thái) |

### A5. Hồ sơ cá nhân — 2 file

| File | Vai trò trong SP1 | Bằng chứng |
|---|---|---|
| `app/Http/Controllers/HoSoCaNhanController.php` | Cập nhật hồ sơ + **upload avatar** + **đổi mật khẩu** | 22–45 (avatar qua `Storage::disk('public')`); 52–66 (`current_password`) |
| `resources/views/ho-so/chinh-sua.blade.php` | 2 form (thông tin cá nhân / đổi mật khẩu) + preview avatar | 9–12; 45–48; 74–80 |

### A6. Model & Migration — 4 file

| File | Vai trò trong SP1 | Bằng chứng |
|---|---|---|
| `app/Models/NguoiDung.php` | Model auth: `Authenticatable`, `Fillable`, cast `password => hashed`, `vai_tro => VaiTro`, `trang_thai => boolean`; quan hệ `student()` | 14–15; 25–34; 36–39 |
| `database/migrations/0001_01_01_000000_create_users_table.php` | Tạo bảng `users` + `sessions` + `password_reset_tokens` | 14–37 |
| `database/migrations/2026_10_18_000001_add_profile_and_role_to_users_table.php` | Thêm `role`, `trang_thai`, `so_dien_thoai`, `avatar`, `last_login_at` | 13–20 |
| `database/migrations/2026_11_01_000000_doi_ten_bang_va_cot_sang_tieng_viet.php` | Đổi `users → nguoi_dung`, `name → ho_ten`, `role → vai_tro`, `avatar → anh_dai_dien`, `last_login_at → dang_nhap_cuoi_luc` (chỉ phần `nguoi_dung`) | 24 (bảng); 44–49 (cột) |

> Migration cuối còn đổi tên bảng của SP2–SP4; **chỉ phần `nguoi_dung` (dòng 24, 44–49)** là thuộc SP1.

### A7. Cấu hình — 4 file

| File | Vai trò trong SP1 | Bằng chứng |
|---|---|---|
| `config/auth.php` | Guard `web` + provider `eloquent` trỏ `App\Models\NguoiDung` | 64–68 |
| `config/qlsv.php` | Mật khẩu mặc định khi **reset** (`11111111`) | 19 (`mat_khau_mac_dinh`) |
| `config/filesystems.php` | Disk `public` cho avatar (`storage/app/public`) | 41–48 |
| `bootstrap/app.php` | Alias middleware `role`; redirect khách → `login`; user đã đăng nhập → `/dashboard` | 16–21 |

---

## B. NHÓM HẠ TẦNG DÙNG CHUNG (SP1 phụ thuộc, không tính riêng SP1)

| Nhóm | File | Vì sao SP1 cần |
|---|---|---|
| Base controller | `app/Http/Controllers/Controller.php` | 3 controller SP1 đều `extends Controller` |
| Provider | `bootstrap/providers.php` | Đăng ký `AppServiceProvider` (rate limit login) |
| Config framework | `config/app.php`, `config/session.php`, `config/cache.php` | Tên app trên `<title>`; session DB; rate limiter dùng cache |
| Migration hạ tầng | `database/migrations/0001_01_01_000001_create_cache_table.php` | `CACHE_STORE=database` → bảng `cache` phục vụ **throttle đăng nhập** và cache dashboard |
| Build frontend | `resources/css/app.css`, `resources/js/app.js`, `vite.config.js`, `package.json`, `package-lock.json` | Layout dùng `@vite(['resources/css/app.css', 'resources/js/app.js'])` |
| Backend dependencies | `composer.json`, `composer.lock` | Autoload `App\*`, framework, PHPUnit (dev) |
| Web entry | `public/index.php`, `public/.htaccess`, `artisan` | Chạy app qua Apache/Laragon và CLI |
| Lưu avatar | `public/storage` (junction → `storage/app/public`), `storage/app/public/.gitignore` | `HoSoCaNhanController` lưu file vào disk `public` |

---

## C. NHÓM PHỤ THUỘC RUNTIME (bắt buộc để SP1 không lỗi, nội dung thuộc SP4)

Sau khi đăng nhập, `DangNhapController:52` redirect `route('dashboard')`; sidebar/topbar cũng gọi `route()` của các module khác. Vì vậy các file sau **phải tồn tại**:

| File | Vì sao liên quan SP1 |
|---|---|
| `app/Http/Controllers/BangDieuKhienController.php` | Đích đến sau đăng nhập; phân nhánh theo 4 vai trò (dòng 17–32) |
| `resources/views/bang-dieu-khien/tong-quan.blade.php` | View đích; chọn partial theo vai trò (dòng 7–13) |
| `resources/views/bang-dieu-khien/_quan-tri.blade.php` | Partial cho Admin / Giáo vụ |
| `resources/views/bang-dieu-khien/_quan-tri-bang.blade.php` | Được `_quan-tri` include |
| `resources/views/bang-dieu-khien/_giang-vien.blade.php` | Partial cho Giảng viên |
| `resources/views/bang-dieu-khien/_sinh-vien.blade.php` | Partial cho Sinh viên |
| `app/Services/ThongKeService.php` | `BangDieuKhienController:13,25,30` |
| `app/Services/GpaService.php` | `BangDieuKhienController:13,19` |
| `app/Models/SinhVien.php` | `NguoiDung::student()` (dòng 36–39) và `NguoiDungController:96` |
| `app/Models/ThongBao.php` + 2 migration `thong_bao`, `thong_bao_nguoi_dung` | Topbar đếm thông báo chưa đọc (`thanh-tren.blade.php:3`) |
| Route của SP2–SP4 (`danh-muc.*`, `students.*`, `courses.*`, `announcements.*`, `portal.*`, `inbox.*`) | `thanh-ben`/`thanh-tren` gọi `route(...)` — thiếu route sẽ lỗi khi render layout |
| `app/Models/Diem.php`, `LopHocPhan.php`, `MonHoc.php`, `GhiDanh.php`, `HocKy.php` | `ThongKeService` / `GpaService` truy vấn qua quan hệ của các model này |

---

## D. KHÔNG thuộc Sprint 1 (tránh nhầm khi lập báo cáo)

| Nhóm | File tiêu biểu | Sprint |
|---|---|---|
| Danh mục | `DanhMucController.php`, `KhoaController`, `NganhController`, `LopController`, `MonHocController`, `HocKyController` + `quan-tri/danh-muc/*` | SP2 |
| Sinh viên | `SinhVienController` + `sinh-vien/*` (5 view) | SP2 |
| Lớp học phần / ghi danh | `LopHocPhanController`, `GhiDanhController` + `lop-hoc-phan/*` | SP2 |
| Điểm | `DiemController`, `DiemService`, `lop-hoc-phan/diem.blade.php` | SP3 |
| Cổng sinh viên | `CongSinhVienController` + `cong-sinh-vien/bang-diem.blade.php` | SP3 |
| Báo cáo | `BaoCaoController` + `bao-cao/*` (2 view) | SP4 |
| Thông báo / hộp thư | `ThongBaoController`, `HopThuController` + `thong-bao/*`, `hop-thu/*` | SP4 |
| Policy cấp lớp HP | `app/Policies/LopHocPhanPolicy.php` | SP2–SP3 (lớp phân quyền thứ 2) |
| Enum khác | `GioiTinh`, `LoaiMonHoc`, `TrangThaiDangKy`, `TrangThaiLopHocPhan`, `TrangThaiSinhVien`, `DoiTuongThongBao` | SP2–SP4 |
| Config điểm | `config/diem.php` | SP3 |
| Dead code | `resources/views/trang-chao.blade.php` | Không thuộc sprint nào |

---

## E. Danh sách copy-paste (27 file cốt lõi)

```
app/Http/Controllers/Auth/DangNhapController.php
app/Http/Controllers/NguoiDungController.php
app/Http/Controllers/HoSoCaNhanController.php
app/Http/Middleware/KiemTraVaiTro.php
app/Enums/VaiTro.php
app/Enums/Concerns/HasLabel.php
app/Models/NguoiDung.php
app/Providers/AppServiceProvider.php
bootstrap/app.php
config/auth.php
config/qlsv.php
config/filesystems.php
routes/web.php
resources/views/bo-cuc/chinh.blade.php
resources/views/bo-cuc/khach.blade.php
resources/views/thanh-phan/thanh-ben.blade.php
resources/views/thanh-phan/thanh-tren.blade.php
resources/views/thanh-phan/thong-bao.blade.php
resources/views/xac-thuc/dang-nhap.blade.php
resources/views/quan-tri/nguoi-dung/danh-sach.blade.php
resources/views/quan-tri/nguoi-dung/bieu-mau.blade.php
resources/views/ho-so/chinh-sua.blade.php
database/migrations/0001_01_01_000000_create_users_table.php
database/migrations/2026_10_18_000001_add_profile_and_role_to_users_table.php
database/migrations/2026_11_01_000000_doi_ten_bang_va_cot_sang_tieng_viet.php
.env.example
.env
```

---

## F. Ghi chú & cách kiểm chứng

1. **Không có thư mục riêng cho Sprint 1.** Code SP1 nằm theo cấu trúc chuẩn của Laravel và đã bị SP2–SP4 sửa chung. Ví dụ:
   - `resources/views/thanh-phan/thanh-ben.blade.php` là file SP1 nhưng chứa menu của cả 4 sprint.
   - `routes/web.php` chứa route của mọi sprint.
   - `database/migrations/2026_11_01_000000_doi_ten_bang_va_cot_sang_tieng_viet.php` đổi tên bảng của cả hệ thống, chỉ phần `nguoi_dung` thuộc SP1.

2. **Không nên tách sprint bằng `git log`.** Commit đầu tiên `4ae936f` (18/10) đã tạo toàn bộ skeleton tiếng Anh (102 file); sau đó mới Việt hóa và làm dần. Sprint chỉ được chia trong tài liệu, không theo commit.

3. **`PHAN_CHIA_SPRINT.md` đã bị xóa** khỏi HEAD ở commit `d3c8faf` (README vẫn còn 3 link trỏ tới file này). Khôi phục bằng:
   ```bat
   git show b84697f:PHAN_CHIA_SPRINT.md > PHAN_CHIA_SPRINT.md
   ```

4. **Mục đích sử dụng danh sách:**
   - Nộp báo cáo Sprint 1 → dùng **nhóm A (27 file)**.
   - Tách riêng / rollback Sprint 1 → cần **A + B + C**, vì thiếu dashboard/routes thì sau khi đăng nhập app sẽ lỗi 500.

5. **Lệnh kiểm chứng nhanh:**
   ```bat
   php artisan route:list --except-vendor     :: 84 route, gồm login/login.store/logout, users.*, profile.*
   php artisan migrate:status                 :: 16 migration Ran
   php artisan view:cache                     :: compile toàn bộ Blade (bắt lỗi thiếu view)
   php artisan view:clear
   php artisan serve
   ```
   Kiểm tra thủ công: mở `/login` → đăng nhập 4 vai trò → mỗi vai trò vào đúng dashboard; sinh viên mở `/users` phải nhận **403**; Admin reset mật khẩu/khoá tài khoản; mọi vai trò cập nhật hồ sơ + đổi mật khẩu + upload avatar.

6. **File không liên quan Sprint 1:** `resources/views/trang-chao.blade.php` (dead code) và 2 file backup JSON trong `storage/app/` (đã phân tích ở báo cáo dọn dẹp trước).

