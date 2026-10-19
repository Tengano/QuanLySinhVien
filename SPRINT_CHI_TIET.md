# Kế hoạch SPRINT CHI TIẾT — Website Quản lý Sinh viên (QLSV)

> Tài liệu này **chi tiết hoá** `AGILE_QLSV.md` (kế hoạch cấp cao) thành từng sprint có thể bắt tay làm ngay:
> mỗi task ghi rõ **ID – ước lượng – file cần tạo – migration – route – tiêu chí nghiệm thu (AC) – test – kịch bản demo**.
>
> | Hạng mục | Giá trị thực tế của repo |
> |---|---|
> | Framework | Laravel **13.35.0** (PHP **8.3.30**, Composer 2.10.2) |
> | CSDL | MySQL 8.4 — database `qlsv`, 3 migration mặc định **đã chạy** |
> | Frontend | **Tailwind CSS 4 + Vite 8** (KHÔNG dùng Bootstrap) |
> | Test | **PHPUnit 12** — chạy trên **SQLite `:memory:`** (`php artisan test`) |
> | Mô hình | Scrum rút gọn: **Sprint 0 (1 tuần) + Sprint 1→6 (2 tuần)** ≈ 13 tuần |
> | Cập nhật | 13/10/2026 |

---

## 0. Trạng thái repo hiện tại (đã kiểm tra trực tiếp)

| # | Hạng mục | Trạng thái | Bằng chứng / Ghi chú |
|---|---|---|---|
| 1 | Laravel cài đặt | ✅ | `php artisan --version` → 13.35.0 |
| 2 | Kết nối MySQL `qlsv` | ✅ | `php artisan migrate:status` → 3 migration "Ran" |
| 3 | File ứng dụng | ❌ | `app/Http/Controllers` chỉ có `Controller.php` |
| 4 | Routes | ❌ | `routes/web.php` chỉ có `GET /` → `welcome` |
| 5 | Views | ❌ | Chỉ có `welcome.blade.php`, chưa có layout |
| 6 | Models | ❌ | Chỉ có `User.php` (chưa có `role`, chưa có enum) |
| 7 | Asset build | ❌ | **`node_modules` và `public/build` đang THIẾU** |
| 8 | Storage link | ❌ | Chưa chạy `php artisan storage:link` (cần cho ảnh SV) |
| 9 | i18n | ❌ | `Locale = en`, `Timezone = UTC`, chưa có `lang/vi` |
| 10 | Package phụ trợ | ❌ | Chưa cài: Excel, DomPDF, (tùy chọn) Spatie Permission |
| 11 | Test | ⚠️ | Chỉ có 2 `ExampleTest.php`, chưa có test nghiệp vụ |
| 12 | Git | ⚠️ | 1 commit khởi tạo `0ec4b61`; chưa có nhánh feature |

**⇒ Kết luận:** Sprint 0 **bắt buộc** làm trước, nếu không mọi sprint sau đều thiếu nền (layout, i18n, asset).

---

## 0.2. Quyết định kỹ thuật PHẢI CHỐT trước Sprint 1 (ADR)

| ADR | Vấn đề | Quyết định (mặc định của kế hoạch này) | Lý do / Rủi ro |
|---|---|---|---|
| **ADR-01** | UI framework | **Giữ Tailwind CSS 4 + Vite 8**, KHÔNG cài Bootstrap | Kế hoạch cũ ghi Bootstrap 5 nhưng scaffold đã là Tailwind → tránh 2 hệ CSS chồng nhau. Component tự viết ở `resources/views/components/` |
| **ADR-02** | Cách làm Auth | **Tự viết bằng Auth guard có sẵn** (`Auth::attempt`, session, `RateLimiter`) | Không phụ thuộc package chưa chắc tương thích Laravel 13; giữ UI Tailwind thuần. Muốn thử Breeze → nhánh riêng `experiment/breeze`, không chặn sprint |
| **ADR-03** | Phân quyền | **`users.role` + enum `App\Enums\Role` + middleware alias `role`** (không cài Spatie ở MVP) | Đủ cho 4 vai trò, ít rủi ro tương thích. Khi cần permission chi tiết → thêm Spatie ở sprint mở rộng, map từ cột `role` |
| **ADR-04** | Công thức điểm | `Điểm TB = QT × 0.4 + Thi × 0.6` (đọc từ `config/diem.php`); bảng quy đổi điểm chữ ở **mục 3.2** | **CẦN giảng viên/giáo vụ xác nhận** trọng số & thang điểm trước Sprint 4 |
| **ADR-05** | Thông báo | 2 bảng `announcements` + `announcement_user` (đã đọc/chưa) | Linh hoạt hơn bảng per-user: gửi 1 lần cho cả lớp/khoa, thống kê được lượt đọc |
| **ADR-06** | Ngôn ngữ | UI tiếng Việt; tên bảng/cột tiếng Việt **không dấu**; `APP_LOCALE=vi`, TZ `Asia/Ho_Chi_Minh` | Nhất quán với ERD đã có; cần `lang/vi` để validate hiển thị tiếng Việt |
| **ADR-07** | Test | PHPUnit 12 + SQLite `:memory:` → **migration phải portable**: dùng `string` + enum PHP thay `ENUM` MySQL, tránh `->fullText()` | Dùng cú pháp riêng MySQL sẽ làm test fail dù code đúng |
| **ADR-08** | Excel / PDF | `maatwebsite/excel` (S3 import, S6 export) + `barryvdh/laravel-dompdf` + font `DejaVu Sans` | Phải kiểm tra tương thích Laravel 13 ngay khi cài (S3-01 có phương án B) |
| **ADR-09** | Upload ảnh | Disk `public`, `storage:link`, path `students/{ma_sv}-{timestamp}.{ext}`, validate `image\|mimes:jpg,jpeg,png,webp\|max:2048`, xoá ảnh cũ khi update/delete | Tránh rác storage; Windows/Laragon tạo symlink OK |
| **ADR-10** | Git workflow | `main` + `feature/sX-mo-ta-ngan`; commit `feat(students): ...`, `fix(auth): ...` | Mỗi sprint tối thiểu 1 lần merge → có lịch sử để báo cáo |
| **ADR-11** | Chốt điểm | `grades.khoa_luc` (timestamp): có giá trị ⇒ **không sửa được điểm** | Bảo vệ toàn vẹn điểm (yêu cầu của giáo vụ) |

---

## 0.3. Quy ước code & cấu trúc thư mục (áp dụng cho mọi sprint)

```
app/
├── Enums/            Role.php, GioiTinh.php, TrangThaiSinhVien.php, TrangThaiLopHocPhan.php, TrangThaiDangKy.php
├── Http/
│   ├── Controllers/  (Admin/ | GiangVien/ | SinhVien/ khi số controller > 10)
│   ├── Middleware/   EnsureUserHasRole.php
│   └── Requests/     <Model>StoreRequest.php, <Model>UpdateRequest.php
├── Imports/          StudentsImport.php
├── Exports/          StudentsExport.php, GradesExport.php
├── Models/           Faculty, Major, SchoolClass, Student, Subject, Semester, Course, Enrollment, Grade, Announcement
├── Policies/         UserPolicy, StudentPolicy, CoursePolicy
└── Services/         DiemService, GpaService, ThongKeService, ThongBaoService, StudentService
config/               diem.php, qlsv.php
lang/vi/              auth.php, validation.php, passwords.php, pagination.php
resources/views/      layouts/, partials/, components/, auth/, users/, danh-muc/, students/, courses/, grades/, dashboard/, reports/
tests/Feature|Unit/   theo module
```

**Quy ước bắt buộc:**
1. Không truy vấn DB trong Blade (trừ `withCount` đã chuẩn bị ở Controller).
2. Mọi `index` dùng `->paginate(15)->withQueryString()`.
3. Mọi form có `@csrf`; form sửa/xoá có `@method`.
4. Validate **luôn** ở Form Request; Controller chỉ dùng `$request->validated()` → chống mass assignment.
5. Thông báo validate tiếng Việt (qua `lang/vi` + `messages()` trong Request).
6. Chạy `vendor/bin/pint` trước mỗi commit; `php artisan test` phải xanh trước khi merge.

---

## 1. Definition of Ready (DoR) & Definition of Done (DoD)

**DoR — task chỉ được đưa vào sprint khi:**
- [ ] Có mã task (S0-01…) + mô tả 1 câu rõ ràng.
- [ ] Biết rõ file/route/màn hình cần tạo.
- [ ] Không vướng task khác chưa xong (phụ thuộc đã hiển thị).
- [ ] Ước lượng ≤ 8 giờ (task lớn hơn phải tách).

**DoD — task chỉ được coi là Done khi:**
- [ ] Chức năng chạy đúng theo AC đã ghi trong tài liệu.
- [ ] Có Feature/Unit test tương ứng và `php artisan test` xanh.
- [ ] `php artisan migrate:fresh --seed` chạy sạch trên MySQL.
- [ ] Có validate (Form Request), chống SQL Injection/XSS/CSRF, không lộ dữ liệu người khác (IDOR).
- [ ] Giao diện responsive, tiếng Việt, không lỗi console.
- [ ] `vendor/bin/pint --test` pass; task được kéo sang cột **Done** trên bảng sprint.

---

## 2. Tổng quan 7 sprint (Sprint 0 → Sprint 6)

| Sprint | Thời lượng | Sprint Goal (mục tiêu 1 câu) | Backlog (Epic) | Ước lượng | Demo cuối sprint |
|---|---|---|---|---|---|
| **Sprint 0** | 1 tuần | Có nền tảng kỹ thuật: asset build, layout Tailwind, i18n tiếng Việt, test pipeline xanh | – (Infra) | ~36,5 h | `php artisan dev` → mở localhost thấy layout + trang 404 tiếng Việt |
| **Sprint 1** | 2 tuần | 4 vai trò đăng nhập được, route được bảo vệ, Admin quản lý tài khoản | E1 | ~68 h | 4 tài khoản demo đăng nhập; SV bị chặn khi vào `/users` |
| **Sprint 2** | 2 tuần | Chuẩn hoá dữ liệu gốc: Khoa → Ngành → Lớp, Môn học, Học kỳ | E2 + E4 | ~73 h | CRUD 5 danh mục, chặn xoá khoa còn ngành |
| **Sprint 3** | 2 tuần | Quản lý hồ sơ sinh viên: CRUD, ảnh, import Excel, tìm kiếm/lọc | E3 | ~78 h | Import 20 dòng (3 lỗi), đổi ảnh, xoá mềm & khôi phục |
| **Sprint 4** | 2 tuần | Lớp học phần + ghi danh + nhập điểm, tính TB/chữ/hệ 4 tự động | E5 + E6 (nhập điểm) | ~84 h | GV nhập điểm 1 lớp → TB/chữ đúng; chốt điểm chặn sửa |
| **Sprint 5** | 2 tuần | SV tra cứu điểm/GPA; dashboard thống kê trực quan | E6 (tra cứu) + E7 | ~69 h | SV in bảng điểm; admin xem 4 biểu đồ |
| **Sprint 6** | 2 tuần | Báo cáo Excel/PDF, thông báo, hardening, tài liệu & bàn giao | E8 + E9 + Harden | ~105 h | Xuất PDF/Excel, gửi thông báo theo lớp, demo trước hội đồng |
| **TỔNG** | ~13 tuần | – | E1 → E9 | **~513,5 h ≈ 85,6 ngày công (6 h/ngày)** | – |

> Số giờ trên là tổng hợp từ `DANH_SACH_TASK_SPRINT.csv` (81 task) — đây là **nguồn số liệu gốc** khi cập nhật tiến độ.

**Quy tắc điều chỉnh phạm vi (quan trọng khi trễ):**
- Hết tuần 1 của sprint mà task ưu tiên `Cao` còn > 50% ⇒ **cắt** các task `Trung bình/Thấp` ghi rõ "cắt" trong tài liệu và chuyển sang sprint sau.
- Ưu tiên bảo toàn: **E1, E2, E3, E6 (nhập điểm)**, còn **E7/E8/E9** có thể hạ xuống mức "Nên có".
- Nếu chỉ có 1 người làm: áp dụng phương án 4 sprint × 1 tuần ở **mục 10.1**.

### 2.1. Cách theo dõi (GitHub Projects / Trello)

Cột: `Backlog → To Do (sprint) → In Progress → Review → Done`.  
Label đề xuất: `type/feature`, `type/bug`, `type/docs`, `type/test`, `area/auth`, `area/danh-muc`, `area/students`, `area/diem`, `area/dashboard`, `area/report`, `priority/cao`, `priority/trung-binh`, `priority/thap`, `sprint/S0…S6`.  
Toàn bộ task đã được liệt kê sẵn trong file `DANH_SACH_TASK_SPRINT.csv` (import trực tiếp).

**Nghi thức mỗi ngày/tuần:**
- Daily 15 phút: *hôm qua làm gì – hôm nay làm gì – có vướng gì*.
- Cuối sprint: **Review** (demo 10–15 phút theo kịch bản trong tài liệu) → **Retro** (giữ / bỏ / thử) → ghi vào `docs/04-nhat-ky-sprint.md`.

---

## 3. SPRINT 0 — Nền tảng kỹ thuật (1 tuần · ~36,5 h)

**Sprint Goal:** *"Chạy được app ở localhost với layout Tailwind, giao diện tiếng Việt, test pipeline xanh — sẵn sàng cho Sprint 1."*

### 3.1. Sprint Backlog

| ID | Task | Loại | Ước lượng | Phụ thuộc | File / Lệnh chính |
|---|---|---|---|---|---|
| S0-01 | Khởi động Laragon (Start All: Apache/Nginx + MySQL), xác nhận PHP/Composer/Node | Infra | 1 h | – | `php -v`, `composer -V`, `node -v` |
| S0-02 | Cài & build asset | Infra | 1 h | S0-01 | `npm install` → `npm run build` (tạo `public/build`) |
| S0-03 | Cấu hình `.env` + `config/app.php` | Config | 1 h | – | `APP_LOCALE=vi`, `APP_FALLBACK_LOCALE=vi`, `APP_FAKER_LOCALE=vi_VN`, timezone `Asia/Ho_Chi_Minh`; giữ `DB_DATABASE=qlsv` |
| S0-04 | Việt hoá thông báo hệ thống | i18n | 3 h | S0-03 | `php artisan lang:publish` → `lang/vi/{auth,validation,passwords,pagination}.php` |
| S0-05 | Storage link cho ảnh SV | Infra | 0.5 h | – | `php artisan storage:link` + tạo `storage/app/public/students` |
| S0-06 | Layout nền (app + guest) | FE | 8 h | S0-02 | `resources/views/layouts/app.blade.php`, `layouts/guest.blade.php`, `partials/sidebar.blade.php`, `partials/topbar.blade.php`, `partials/flash.blade.php`, `partials/breadcrumb.blade.php` |
| S0-07 | Bộ component Blade tái sử dụng | FE | 6 h | S0-06 | `components/{button,input,select,textarea,card,alert,badge,table,pagination-link}.blade.php` |
| S0-08 | Khung route + trang chủ | BE | 2 h | – | `routes/web.php`: `/` → `redirect()->route('login')`; chia group `auth`, `role` (để trống, điền ở S1) |
| S0-09 | Trang lỗi tiếng Việt | FE | 2 h | S0-06 | `resources/views/errors/{403,404,419,429,500}.blade.php` |
| S0-10 | Enum & hằng số nền | BE | 3 h | – | `php artisan make:enum Role` → `app/Enums/{Role,GioiTinh,TrangThaiSinhVien,TrangThaiLopHocPhan,TrangThaiDangKy}.php` |
| S0-11 | File cấu hình nghiệp vụ | BE | 2 h | S0-10 | `config/diem.php` (trọng số + bảng quy đổi), `config/qlsv.php` (tên trường, logo, số dòng/trang) |
| S0-12 | Git & bảng sprint | Docs | 2 h | – | Branch `feature/s0-nen-tang`, commit `chore(setup): nền tảng Sprint 0`; tạo GitHub Project + import `DANH_SACH_TASK_SPRINT.csv` |
| S0-13 | Test pipeline + test sức khoẻ | Test | 2 h | S0-02 | `tests/Feature/HealthTest.php` (`GET /up` = 200); xoá 2 `ExampleTest` mặc định; `php artisan test` |
| S0-14 | Tài liệu cài đặt | Docs | 3 h | – | `README.md` (5 bước cài đặt), `docs/00-tong-quan.md` |

### 3.2. Nội dung kỹ thuật chính

**Lệnh chạy dev hàng ngày (Laravel 13 đã có sẵn lệnh `dev`):**
```bat
composer run dev        :: chạy song song server + vite + queue + log (khuyến nghị)
:: hoặc thủ công 2 terminal:
php artisan serve       :: http://127.0.0.1:8000
npm run dev
```

**Mẫu `layouts/app.blade.php` (khung tối thiểu, mở rộng dần):**
```blade
<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Quản lý sinh viên') — {{ config('qlsv.ten_truong') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-100 text-slate-800 antialiased">
<div class="flex min-h-full">
    @include('partials.sidebar')
    <div class="flex flex-1 flex-col">
        @include('partials.topbar')
        <main class="flex-1 p-4 lg:p-6">
            @include('partials.flash')
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
```

**Bảng quy đổi điểm (đặt trong `config/diem.php` — chờ xác nhận theo ADR-04):**

| Điểm TB (hệ 10) | Điểm chữ | Hệ 4 | Xếp loại môn |
|---|---|---|---|
| 8.5 – 10 | A | 4.0 | Đạt |
| 8.0 – 8.4 | B+ | 3.5 | Đạt |
| 7.0 – 7.9 | B | 3.0 | Đạt |
| 6.5 – 6.9 | C+ | 2.5 | Đạt |
| 5.5 – 6.4 | C | 2.0 | Đạt |
| 5.0 – 5.4 | D+ | 1.5 | Đạt |
| 4.0 – 4.9 | D | 1.0 | Đạt |
| < 4.0 | F | 0.0 | Không đạt |

### 3.3. Acceptance Criteria (AC)

- **AC0-1:** `npm run build` thành công và tạo thư mục `public/build`; trang `/` render có Tailwind (kiểm tra bằng class `bg-slate-100` đổi màu).
- **AC0-2:** `php artisan about --only=environment` hiển thị `Locale = vi` và `Timezone = Asia/Ho_Chi_Minh`.
- **AC0-3:** `php artisan test` → tất cả pass (bao gồm `HealthTest`).
- **AC0-4:** Truy cập `/khong-ton-tai` → trang 404 có tiêu đề tiếng Việt, có nút "Về trang chủ".
- **AC0-5:** Form validate mẫu (thử ở S1) hiển thị thông báo lỗi tiếng Việt ("Trường Họ tên là bắt buộc").

### 3.4. Kịch bản demo (5 phút)

1. Mở terminal: `composer run dev` → chụp banner Laravel 13 + Vite ready.
2. Mở `http://127.0.0.1:8000` → thấy layout khung (sidebar + topbar) chưa có dữ liệu.
3. Nhập URL sai → thấy trang 404 tiếng Việt.
4. Mở `php artisan about` → chỉ locale/timezone.
5. Mở bảng GitHub Projects → kéo S0-01…S0-14 sang Done.

### 3.5. Rủi ro & xử lý

| Rủi ro | Mức | Xử lý |
|---|---|---|
| `npm install` lâu/lỗi mạng | TB | Dùng `npm install --registry=https://registry.npmmirror.com` hoặc cài 1 lần rồi commit `package-lock.json` |
| Symlink `storage:link` bị chặn (Windows) | Thấp | Chạy terminal **Admin** hoặc dùng `FILESYSTEM_DISK=local` + route stream ảnh |
| Quên build asset ⇒ CSS không có tác dụng | TB | Ghi vào checklist DoD: "đã `npm run build`" |

---

## 4. SPRINT 1 — Xác thực & Phân quyền (2 tuần · ~68 h)

**Sprint Goal:** *"4 vai trò (Admin / Giáo vụ / Giảng viên / Sinh viên) đăng nhập được, mọi route nghiệp vụ được bảo vệ, Admin quản lý được tài khoản."*

### 4.1. Sprint Backlog

| ID | Task | Loại | Ước lượng | Phụ thuộc | File / Route chính |
|---|---|---|---|---|---|
| S1-01 | Migration bổ sung cột cho `users` | DB | 2 h | S0-10 | `database/migrations/xxxx_add_profile_and_role_to_users_table.php` |
| S1-02 | Enum `Role` + nâng cấp model `User` | BE | 3 h | S1-01 | `app/Enums/Role.php`, `app/Models/User.php` |
| S1-03 | Middleware `EnsureUserHasRole` + alias | BE | 3 h | S1-02 | `app/Http/Middleware/EnsureUserHasRole.php`, `bootstrap/app.php` |
| S1-04 | Đăng nhập / đăng xuất + rate limit | BE | 6 h | S1-03 | `app/Http/Controllers/Auth/LoginController.php`, `AppServiceProvider::boot()` |
| S1-05 | Dashboard 4 biến thể theo vai trò | BE+FE | 3 h | S1-04 | `app/Http/Controllers/DashboardController.php`, `resources/views/dashboard/index.blade.php` |
| S1-06 | Quản lý tài khoản (CRUD + reset mật khẩu + khoá) | BE | 12 h | S1-02 | `app/Http/Controllers/UserController.php`, `Requests/{StoreUserRequest,UpdateUserRequest}.php` |
| S1-07 | `UserPolicy` chống tự xoá/tự hạ quyền | BE | 3 h | S1-06 | `app/Policies/UserPolicy.php` |
| S1-08 | Hồ sơ cá nhân & đổi mật khẩu | BE+FE | 4 h | S1-04 | `app/Http/Controllers/ProfileController.php`, `resources/views/profile/edit.blade.php` |
| S1-09 | Sidebar/topbar động theo vai trò | FE | 6 h | S1-06 | `partials/sidebar.blade.php`, `partials/topbar.blade.php` |
| S1-10 | Seeder 4 tài khoản demo | DB | 3 h | S1-06 | `database/seeders/UserSeeder.php` + `DatabaseSeeder` |
| S1-11 | Views: login, users, profile | FE | 10 h | S1-06 | `auth/login.blade.php`, `users/{index,create,edit,_form}.blade.php` |
| S1-12 | Route + rate limiter `login` | BE | 3 h | S1-04 | `routes/web.php`, `AppServiceProvider::boot()` |
| S1-13 | Feature test cho auth & phân quyền | Test | 10 h | S1-01→S1-12 | `tests/Feature/Auth/*`, `tests/Feature/UserManagementTest.php` |

### 4.2. Migration S1-01 (nội dung chính)

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('role', 20)->default(App\Enums\Role::SINH_VIEN->value)->index()->after('password');
    $table->boolean('trang_thai')->default(true);          // false = bị khoá
    $table->string('so_dien_thoai', 15)->nullable();
    $table->string('avatar')->nullable();
    $table->timestamp('last_login_at')->nullable();
    $table->index(['role', 'trang_thai']);                  // phục vụ filter ở màn quản lý tài khoản
});
```
> Dùng `string` + enum PHP (**không** dùng `ENUM` của MySQL) để test SQLite chạy được (ADR-07).

### 4.3. Middleware S1-03

```php
// app/Http/Middleware/EnsureUserHasRole.php
public function handle(Request $request, Closure $next, string ...$roles): Response
{
    $user = $request->user();
    abort_if(! $user || ! in_array($user->role->value, $roles, true), 403,
        'Bạn không có quyền truy cập chức năng này.');

    return $next($request);
}
```
```php
// bootstrap/app.php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias(['role' => \App\Http\Middleware\EnsureUserHasRole::class]);
    $middleware->redirectGuestsTo('/login');
    $middleware->redirectUsersTo('/dashboard');
})
```

### 4.4. Route S1-12 (khung chuẩn cho các sprint sau)

```php
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:login');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/ho-so', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/ho-so', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/ho-so/mat-khau', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except('show');
        Route::patch('users/{user}/trang-thai', [UserController::class, 'toggleStatus'])->name('users.trang-thai');
        Route::patch('users/{user}/dat-lai-mat-khau', [UserController::class, 'resetPassword'])->name('users.dat-lai-mat-khau');
    });
});
```
```php
// AppServiceProvider::boot()
RateLimiter::for('login', fn (Request $request) => Limit::perMinute(5)
    ->by($request->input('email').'|'.$request->ip()));
```

### 4.5. Tài khoản demo (S1-10)

| Vai trò | Email | Mật khẩu | Trang mặc định |
|---|---|---|---|
| Admin | `admin@qlsv.test` | `password` | `/users` |
| Giáo vụ | `giaovu@qlsv.test` | `password` | `/dashboard` |
| Giảng viên | `giangvien@qlsv.test` | `password` | `/dashboard` |
| Sinh viên | `sinhvien@qlsv.test` | `password` | `/dashboard` |

### 4.6. Acceptance Criteria

- **AC1-1:** Given vai trò *sinh viên* đã đăng nhập, When mở `/users`, Then **403** + trang lỗi tiếng Việt "Bạn không có quyền truy cập chức năng này".
- **AC1-2:** Given tài khoản có `trang_thai = false`, When đăng nhập, Then báo lỗi *"Tài khoản đã bị khoá, liên hệ giáo vụ"* và **không** tạo session.
- **AC1-3:** Given khách chưa đăng nhập, When mở `/dashboard`, Then chuyển tới `/login` và sau khi đăng nhập quay lại đúng `/dashboard` (intended URL).
- **AC1-4:** Sau 5 lần đăng nhập sai trong 1 phút, lần thứ 6 trả **429** kèm thông báo chờ 60 giây.
- **AC1-5:** Admin không thể xoá/khóa chính mình; nếu thao tác sẽ hiển thị lỗi "Không thể xoá tài khoản đang đăng nhập".
- **AC1-6:** Sau khi đăng nhập, `last_login_at` được cập nhật; `session()->regenerate()` được gọi (kiểm tra bằng test `assertAuthenticatedAs` + kiểm tra session id đổi).
- **AC1-7:** Mật khẩu lưu dạng hash (BCrypt), không bao giờ hiển thị lại trong form.

### 4.7. Test cần viết (S1-13)

| File test | Nội dung |
|---|---|
| `tests/Feature/Auth/LoginTest.php` | đăng nhập thành công → `/dashboard`; sai mật khẩu → lỗi tiếng Việt; tài khoản bị khoá → chặn; khoá rate limit sau 5 lần → 429 |
| `tests/Feature/Auth/AuthorizationTest.php` | guest → redirect login; sinh viên/giảng viên vào `/users` → 403; admin → 200 |
| `tests/Feature/UserManagementTest.php` | tạo user (validate email trùng, password `confirmed`, `min:6`); reset mật khẩu; khoá/mở khoá; không xoá chính mình |
| `tests/Feature/ProfileTest.php` | đổi mật khẩu sai `current_password` → lỗi; đúng → đăng nhập được bằng mật khẩu mới; cập nhật thông tin không đổi được `role` (chống mass assignment) |

**Lệnh chạy:**
```bat
php artisan test --filter=Auth
php artisan test
vendor/bin/pint --dirty
```

### 4.8. Kịch bản demo (8 phút)

1. Đăng nhập sai 5 lần → chỉ ra màn hình 429/thông báo chờ.
2. Đăng nhập `admin@qlsv.test` → thấy menu **Quản lý tài khoản**; tạo 1 tài khoản giảng viên mới.
3. Mở cửa sổ ẩn danh, đăng nhập `sinhvien@qlsv.test` → thanh menu **không** có Quản lý tài khoản; gõ tay `/users` → 403.
4. Đăng nhập `giaovu@qlsv.test` → thấy menu giáo vụ, KHÔNG có Quản lý tài khoản.
5. Đổi mật khẩu hồ sơ, đăng xuất, đăng nhập lại bằng mật khẩu mới.
6. Chạy `php artisan test --filter=Auth` → show kết quả xanh.

### 4.9. Rủi ro & xử lý

| Rủi ro | Mức | Xử lý |
|---|---|---|
| Session fixation | Cao | Bắt buộc `$request->session()->regenerate()` ngay sau login; `invalidate()` + `regenerateToken()` khi logout |
| Quên bảo vệ route mới | Cao | Đặt mọi route nghiệp vụ trong group `['auth','role:...']`; viết test authorization cho từng module ở các sprint sau |
| Mass assignment `role` khi user tự sửa hồ sơ | Cao | `ProfileController` chỉ nhận `name`, `email`, `so_dien_thoai`, `avatar` |
| Chưa có `must_change_password` đầu kỳ | Thấp | Ghi vào backlog giai đoạn 2 (Sprint 7+) |

---

## 5. SPRINT 2 — Danh mục: Khoa · Ngành · Lớp · Môn học · Học kỳ (2 tuần · ~73 h)

**Sprint Goal:** *"Chuẩn hoá toàn bộ dữ liệu gốc để Sprint 3–5 chỉ việc tham chiếu khoá ngoại."*

### 5.1. Sprint Backlog

| ID | Task | Loại | Ước lượng | File chính |
|---|---|---|---|---|
| S2-01 | Migration + Model Khoa (`faculties`) | DB+BE | 3 h | `..._create_faculties_table.php`, `app/Models/Faculty.php` |
| S2-02 | Migration + Model Ngành (`majors`) | DB+BE | 3 h | `..._create_majors_table.php`, `app/Models/Major.php` |
| S2-03 | Migration + Model Lớp hành chính (`classes`) | DB+BE | 3 h | `..._create_classes_table.php`, `app/Models/SchoolClass.php` |
| S2-04 | Migration + Model Môn học (`subjects`) | DB+BE | 3 h | `..._create_subjects_table.php`, `app/Models/Subject.php` |
| S2-05 | Migration + Model Học kỳ (`semesters`) | DB+BE | 2 h | `..._create_semesters_table.php`, `app/Models/Semester.php` |
| S2-06 | Factory + Seeder dữ liệu mẫu danh mục | DB | 6 h | `database/factories/*`, `database/seeders/{Faculty,Major,SchoolClass,Subject,Semester}Seeder.php` |
| S2-07 | Form Request × 5 (validate + unique + tiếng Việt) | BE | 6 h | `app/Http/Requests/{Faculty,Major,SchoolClass,Subject,Semester}{Store,Update}Request.php` |
| S2-08 | Controller × 5 (index/create/store/edit/update/destroy/show) | BE | 14 h | `app/Http/Controllers/{Faculty,Major,SchoolClass,Subject,Semester}Controller.php` |
| S2-09 | Views × 5 module (index + create + edit + `_form`) | FE | 16 h | `resources/views/danh-muc/{khoa,nganh,lop,mon-hoc,hoc-ky}/*` |
| S2-10 | Route nhóm `danh-muc` + menu sidebar | BE+FE | 3 h | `routes/web.php`, `partials/sidebar.blade.php` |
| S2-11 | Chặn xoá khi còn dữ liệu con (thông báo thân thiện) | BE | 4 h | Xử lý trong Controller (kiểm tra `->majors()->exists()`) |
| S2-12 | Feature test × 5 module + test authorization | Test | 10 h | `tests/Feature/DanhMuc/*` |

### 5.2. Lược đồ bảng (DDL tóm tắt)

| Bảng | Cột | Ràng buộc quan trọng |
|---|---|---|
| `faculties` | id, `ma_khoa`, `ten_khoa`, `truong_khoa`?, `dien_thoai`?, `email`?, `dia_chi`?, timestamps | `ma_khoa` **unique**, index `ten_khoa` |
| `majors` | id, `faculty_id`, `ma_nganh`, `ten_nganh`, `so_nam_dao_tao` (default 4), timestamps | `ma_nganh` unique; FK → faculties `restrictOnDelete`; unique(`faculty_id`,`ten_nganh`) |
| `classes` | id, `major_id`, `ma_lop`, `ten_lop`, `khoa_hoc` (smallint, vd 2023), `gvcn_id`?, timestamps | `ma_lop` unique; FK → majors `restrictOnDelete`; FK → users `nullOnDelete` |
| `subjects` | id, `faculty_id`?, `ma_mh`, `ten_mh`, `so_tin_chi` (tinyint), `loai_mh` (string: `bat_buoc`/`tu_chon`), `so_tiet_ly_thuyet`?, `so_tiet_thuc_hanh`?, timestamps | `ma_mh` unique; FK → faculties `nullOnDelete` |
| `semesters` | id, `ten_hoc_ky` (HK1/HK2/HK Hè), `hoc_ky_so` (tinyint 1–3), `nam_hoc` (string(9) `2026-2027`), `ngay_bat_dau`, `ngay_ket_thuc`, timestamps | unique(`nam_hoc`,`hoc_ky_so`); `ngay_ket_thuc` phải `after:ngay_bat_dau` (validate) |

> **CẢNH BÁO KỸ THUẬT:** `Class` là **từ khoá của PHP** ⇒ model đặt tên **`SchoolClass`** với `protected $table = 'classes';`. Route/resource name dùng `lop` (vd `danh-muc.lop.index`).

### 5.3. Quan hệ (Eloquent)

```php
Faculty:     hasMany(Major::class), hasMany(Subject::class)
Major:       belongsTo(Faculty::class), hasMany(SchoolClass::class)
SchoolClass: belongsTo(Major::class), belongsTo(User::class,'gvcn_id'), hasMany(Student::class)
Subject:     belongsTo(Faculty::class), hasMany(Course::class)
Semester:    hasMany(Course::class)
```

### 5.4. Route S2-10

```php
Route::middleware(['auth', 'role:admin,giao_vu'])
    ->prefix('danh-muc')->name('danh-muc.')->group(function () {
        Route::resource('khoa', FacultyController::class)->parameters(['khoa' => 'faculty']);
        Route::resource('nganh', MajorController::class)->parameters(['nganh' => 'major']);
        Route::resource('lop', SchoolClassController::class)->parameters(['lop' => 'schoolClass']);
        Route::resource('mon-hoc', SubjectController::class)->parameters(['mon-hoc' => 'subject']);
        Route::resource('hoc-ky', SemesterController::class)->parameters(['hoc-ky' => 'semester']);
    });
```

### 5.5. Pattern chuẩn cho mọi màn `index` (dùng lại ở S3–S6)

```php
public function index(Request $request): View
{
    $faculties = Faculty::query()
        ->when($request->filled('kw'), fn ($q) => $q->where(fn ($w) => $w
            ->where('ma_khoa', 'like', "%{$request->kw}%")
            ->orWhere('ten_khoa', 'like', "%{$request->kw}%")))
        ->withCount(['majors', 'subjects'])
        ->orderBy('ma_khoa')
        ->paginate(15)
        ->withQueryString();

    return view('danh-muc.khoa.index', compact('faculties'));
}
```

### 5.6. Seeder dữ liệu mẫu (S2-06)

| Danh mục | Số lượng | Ví dụ |
|---|---|---|
| Khoa | 3 | CNTT, Kinh tế, Cơ khí |
| Ngành | 6 | Công nghệ thông tin, Kỹ thuật phần mềm, Kế toán, Tài chính ngân hàng, Cơ điện tử, Công nghệ ô tô |
| Lớp | 12 | `CNTT-K23-A`, `CNTT-K23-B`, `KT-K24-A`… |
| Môn học | 20 | Lập trình Web (3 TC), Cấu trúc dữ liệu (4 TC), CSDL (3 TC)… |
| Học kỳ | 3 | HK1 2025-2026, HK2 2025-2026, HK1 2026-2027 |

### 5.7. Acceptance Criteria

- **AC2-1:** CRUD đủ 5 danh mục; dữ liệu nhập sai (mã trùng, bỏ trống, chọn ngành thuộc khoa khác) hiển thị lỗi tiếng Việt ngay dưới ô nhập.
- **AC2-2:** Xoá Khoa còn Ngành → **không xoá được**, hiện *"Không thể xoá: Khoa còn 2 ngành"*, DB không văng lỗi 1451.
- **AC2-3:** Ô chọn Ngành trong form Lớp hiển thị nhóm theo Khoa (`<optgroup>`) để tránh nhầm.
- **AC2-4:** Tìm kiếm `cntt` (chữ thường) vẫn trả về `CNTT` (không phân biệt hoa/thường nhờ collation `utf8mb4_unicode_ci`).
- **AC2-5:** Học kỳ có `ngay_ket_thuc` trước `ngay_bat_dau` → lỗi validate; trùng `nam_hoc + hoc_ky_so` → lỗi validate.
- **AC2-6:** Sau `php artisan migrate:fresh --seed` có đủ dữ liệu mẫu ở mục 5.6; mọi màn `index` phân trang 15 dòng và giữ filter khi chuyển trang.

### 5.8. Test cần viết (S2-12)

| File test | Nội dung |
|---|---|
| `tests/Feature/DanhMuc/FacultyCrudTest.php` | tạo/sửa/xoá khoa; validate `ma_khoa` trùng; chặn xoá khi còn ngành |
| `tests/Feature/DanhMuc/MajorCrudTest.php` | validate `faculty_id` exists; unique `ma_nganh` |
| `tests/Feature/DanhMuc/SchoolClassCrudTest.php` | validate `major_id`; `khoa_hoc` là 4 chữ số |
| `tests/Feature/DanhMuc/SubjectCrudTest.php` | `so_tin_chi` trong 1..6; unique `ma_mh` |
| `tests/Feature/DanhMuc/SemesterCrudTest.php` | `ngay_ket_thuc after ngay_bat_dau`; unique tổ hợp |
| `tests/Feature/DanhMuc/DanhMucAuthorizationTest.php` | giảng viên/sinh viên → 403 mọi route danh mục; giáo vụ → 200 |

### 5.9. Kịch bản demo (8 phút)

1. Tạo Khoa *CNTT* → Ngành *Kỹ thuật phần mềm* → Lớp `KTMP-K25-A` → gán GVCN.
2. Tạo Môn *Lập trình Web 3 tín chỉ* → Học kỳ *HK1 2026-2027*.
3. Thử xoá Khoa CNTT → thấy thông báo chặn (AC2-2).
4. Nhập trùng mã khoa → thấy lỗi validate tiếng Việt.
5. Tìm `ktt` → lọc đúng; chuyển trang 2 → filter còn nguyên.

### 5.10. Rủi ro & xử lý

| Rủi ro | Mức | Xử lý |
|---|---|---|
| Dùng `Class` làm tên model → lỗi cú pháp PHP | Cao | Dùng `SchoolClass` (đã nêu ở 5.2) |
| FK `restrictOnDelete` gây lỗi 500 khi xoá | TB | Luôn kiểm tra dữ liệu con trước khi `delete()`, trả `back()->withError(...)` |
| Seed lại bị lỗi trùng dữ liệu | TB | Seeder dùng `updateOrCreate` theo mã |
| Test SQLite không hỗ trợ cột đặc thù MySQL | TB | Giữ migration portable (ADR-07) |

---

## 6. SPRINT 3 — Quản lý Sinh viên (2 tuần · ~78 h)

**Sprint Goal:** *"Giáo vụ thay thế hoàn toàn file Excel thủ công: thêm/sửa/tra cứu hồ sơ SV, import hàng loạt, có ảnh đại diện và tìm kiếm/lọc đa tiêu chí."*

### 6.1. Sprint Backlog

| ID | Task | Loại | Ước lượng | File chính |
|---|---|---|---|---|
| S3-01 | Cài & kiểm tra `maatwebsite/excel` (có phương án B) | Infra | 2 h | `composer require maatwebsite/excel` |
| S3-02 | Migration + Model `students` | DB+BE | 4 h | `..._create_students_table.php`, `app/Models/Student.php` |
| S3-03 | Enum `GioiTinh`, `TrangThaiSinhVien` + accessor/scope lọc | BE | 5 h | `app/Enums/*`, `Student::scopeFilter()` |
| S3-04 | Form Request store/update + validate ảnh | BE | 5 h | `app/Http/Requests/{StoreStudentRequest,UpdateStudentRequest}.php` |
| S3-05 | `StudentService`: upload/xoá ảnh, tạo tài khoản SV | BE | 6 h | `app/Services/StudentService.php` |
| S3-06 | `StudentController` CRUD + soft delete + khôi phục | BE | 8 h | `app/Http/Controllers/StudentController.php` |
| S3-07 | Import Excel: `StudentsImport` + file mẫu + báo lỗi theo dòng | BE | 14 h | `app/Imports/StudentsImport.php`, `app/Exports/StudentsTemplateExport.php`, `app/Http/Controllers/StudentImportController.php` |
| S3-08 | Views: index (bảng + filter), create/edit/_form, show, import, trash | FE | 18 h | `resources/views/students/*` |
| S3-09 | Route nhóm `sinh-vien` + menu | BE+FE | 3 h | `routes/web.php` |
| S3-10 | Feature/Unit test cho module SV | Test | 13 h | `tests/Feature/Students/*` |

### 6.2. Migration + Model `students` (S3-02)

| Cột | Kiểu | Ghi chú |
|---|---|---|
| `id` | bigint PK | |
| `user_id` | FK users, nullable, unique, `nullOnDelete` | Liên kết tài khoản đăng nhập |
| `ma_sv` | string(20) unique | Mã SV, index |
| `ho_ten` | string(150), index | |
| `ngay_sinh` | date | |
| `gioi_tinh` | string(10) | `nam` / `nu` / `khac` |
| `lop_id` | FK classes `restrictOnDelete` | Composite index (`lop_id`,`trang_thai`) |
| `email` | string, nullable | Dùng làm email đăng nhập nếu tạo tài khoản |
| `sdt` | string(15), nullable | |
| `cccd` | string(20), nullable, unique | |
| `dia_chi` | text, nullable | |
| `anh` | string, nullable | Đường dẫn tương đối trên disk `public` |
| `trang_thai` | string(20) default `dang_hoc` | `dang_hoc`/`bao_luu`/`tot_nghiep`/`thoi_hoc` |
| `ngay_nhap_hoc` | date, nullable | |
| `ghi_chu` | text, nullable | |
| `deleted_at` | softDeletes | Xoá mềm |

**Quy tắc nghiệp vụ:**
- `ma_sv` **chặn tuyệt đối** trùng (kể cả với SV đã xoá mềm) để bảo toàn lịch sử.
- Tạo SV với checkbox `tao_tai_khoan` = 1 ⇒ bắt buộc có `email`; tạo `users` `role = sinh_vien`, mật khẩu mặc định `Qlsv@{ddmmyyyy}`, gán `students.user_id`.
- Ảnh lưu `students/{ma_sv}-{timestamp}.{ext}`; update/delete phải xoá file cũ (`Storage::disk('public')->delete()`).

### 6.3. Route nhóm `sinh-vien` (S3-09)

```php
Route::middleware(['auth', 'role:admin,giao_vu'])->group(function () {
    Route::get('sinh-vien/thung-rac', [StudentController::class, 'trash'])->name('sinh-vien.thung-rac');
    Route::patch('sinh-vien/{id}/khoi-phuc', [StudentController::class, 'restore'])->name('sinh-vien.khoi-phuc');
    Route::get('sinh-vien/import', [StudentImportController::class, 'create'])->name('sinh-vien.import');
    Route::post('sinh-vien/import', [StudentImportController::class, 'store'])->name('sinh-vien.import.store');
    Route::get('sinh-vien/import/file-mau', [StudentImportController::class, 'template'])->name('sinh-vien.import.mau');
    Route::resource('sinh-vien', StudentController::class);

    Route::middleware('role:admin')->group(function () {
        Route::delete('sinh-vien/{id}/xoa-vinh-vien', [StudentController::class, 'forceDelete'])->name('sinh-vien.xoa-vinh-vien');
    });
});
```
> Thứ tự route quan trọng: các route literal (`thung-rac`, `import`) phải khai báo **trước** `Route::resource` để không bị `{sinh_vien}` "nuốt".

### 6.4. Luồng Import Excel (S3-07)

1. `GET /sinh-vien/import` → form tải file + link *"Tải file mẫu"*.
2. `POST /sinh-vien/import` → `ImportStudentsRequest` validate `file` = `mimes:xlsx,xls,csv|max:5120`.
3. `StudentsImport` implements `ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows`:
   - Cột chuẩn: `ma_sv, ho_ten, ngay_sinh, gioi_tinh, ma_lop, email, sdt, cccd, dia_chi, trang_thai`.
   - `ngay_sinh` nhận cả `dd/mm/yyyy` và `yyyy-mm-dd`.
   - Tra `ma_lop` → `lop_id`; nếu không tồn tại ⇒ ghi lỗi dòng đó và **bỏ qua dòng đó**, các dòng khác vẫn nhập.
   - Kết quả trả về gồm: `imported`, `skipped`, `errors[] = ['dong' => n, 'loi' => '...']`.
4. Trang kết quả hiển thị bảng *dòng nào lỗi – vì sao*.

> **Phương án B (nếu package không tương thích Laravel 13):** import CSV thuần bằng `fgetcsv()` với cùng cột chuẩn — thêm ~4 h, không ảnh hưởng AC.

### 6.5. Acceptance Criteria

- **AC3-1:** Import file mẫu 20 dòng (3 dòng lỗi: trùng `ma_sv`, `ma_lop` không tồn tại, thiếu `ho_ten`) ⇒ tạo đúng **17** SV, bảng lỗi hiển thị đúng **3** dòng kèm lý do; không mất dữ liệu hợp lệ.
- **AC3-2:** Upload ảnh 2 MB (PNG) thành công, ảnh hiển thị ở danh sách + trang chi tiết; upload file `.exe` bị từ chối với thông báo "Ảnh phải là tệp hình ảnh…".
- **AC3-3:** Xoá mềm SV ⇒ biến mất khỏi danh sách chính, xuất hiện ở "Thùng rác"; bấm **Khôi phục** ⇒ trở lại danh sách với đúng dữ liệu cũ.
- **AC3-4:** Tìm `nguyễn` ⇒ ra mọi SV họ Nguyễn; lọc theo *Khoa → Lớp* + *giới tính* + *trạng thái* kết hợp vẫn đúng; phân trang giữ filter.
- **AC3-5:** Tạo SV có `tao_tai_khoan` ⇒ tài khoản `role = sinh_vien` được tạo và SV đăng nhập được bằng mật khẩu mặc định.
- **AC3-6:** Truy cập `/sinh-vien/{id}/sua` của SV bất kỳ bằng vai trò giảng viên ⇒ **403**.

### 6.6. Test cần viết (S3-10)

| File test | Nội dung |
|---|---|
| `tests/Feature/Students/StudentCrudTest.php` | tạo/xem/sửa/xoá mềm; mã SV trùng → lỗi; `lop_id` không tồn tại → lỗi |
| `tests/Feature/Students/StudentFilterTest.php` | lọc theo keyword + `lop_id` + `trang_thai`; `assertDontSee` SV bị lọc bỏ |
| `tests/Feature/Students/StudentPhotoTest.php` | `Storage::fake('public')`; upload OK → file tồn tại; upload file sai loại → lỗi validate; cập nhật ảnh ⇒ ảnh cũ bị xoá |
| `tests/Feature/Students/StudentImportTest.php` | dùng `UploadedFile::fake()->createWithContent('sv.csv', $csv)`; case hợp lệ, thiếu cột, trùng mã, mã lớp sai ⇒ kiểm tra số lượng + thông báo lỗi |
| `tests/Feature/Students/StudentSoftDeleteTest.php` | `assertSoftDeleted`; khôi phục; chỉ admin xoá vĩnh viễn; giáo vụ xoá vĩnh viễn → 403 |
| `tests/Feature/Students/StudentAccountTest.php` | tạo tài khoản kèm SV ⇒ `users` có bản ghi `role = sinh_vien` + `students.user_id` khớp |
| `tests/Unit/StudentAttributeTest.php` | `tuoi` tính đúng theo `ngay_sinh`; label giới tính/trạng thái tiếng Việt |

### 6.7. Kịch bản demo (10 phút)

1. Mở `/sinh-vien` (đang trống) → thêm 1 SV thủ công kèm ảnh + tick *Tạo tài khoản*.
2. Tải file mẫu → điền 20 dòng (có 3 dòng lỗi) → import → chỉ ra bảng kết quả: 17 thành công, 3 lỗi có lý do.
3. Lọc: Khoa = CNTT, Lớp = `CNTT-K23-A`, Trạng thái = Đang học, keyword `nguyễn`.
4. Mở chi tiết 1 SV → xem thông tin + (phần điểm sẽ có ở Sprint 5).
5. Xoá mềm 1 SV → mở Thùng rác → khôi phục.
6. Cửa sổ ẩn danh: đăng nhập tài khoản SV vừa tạo → đăng nhập thành công; thử mở `/sinh-vien` → 403.

### 6.8. Rủi ro & xử lý

| Rủi ro | Mức | Xử lý |
|---|---|---|
| File Excel của người dùng sai định dạng cột | Cao | Bắt buộc dùng file mẫu; `WithHeadingRow` + kiểm tra tên cột ở `WithValidation`; báo lỗi rõ dòng/cột |
| Import 5000 dòng gây timeout | TB | Chặn `max:5 MB`; với file lớn chuyển sang `queue` (ghi chú Sprint 7+) |
| Rác file ảnh khi xoá SV | TB | Xoá file trong `StudentService` (hook `deleting`/`forceDeleting`) |
| Trùng `ma_sv` giữa file và DB | TB | Kiểm tra `exists` trước khi ghi; dòng trùng bị đưa vào báo lỗi |
| Rò rỉ dữ liệu cá nhân (CCCD, SĐT) | TB | Chỉ `role:admin,giao_vu` xem được; không đưa các cột này vào trang tra cứu công khai |

---

## 7. SPRINT 4 — Lớp học phần · Ghi danh · Nhập điểm (2 tuần · ~84 h)

**Sprint Goal:** *"Giảng viên nhập điểm QT/Thi cho lớp mình dạy, hệ thống tự tính Điểm TB – Điểm chữ – Hệ 4 – Đạt/Không đạt và chốt điểm để chống sửa."*

### 7.1. Sprint Backlog

| ID | Task | Loại | Ước lượng | File chính |
|---|---|---|---|---|
| S4-01 | Migration `courses` + `enrollments` + `grades` | DB | 5 h | `..._create_courses_table.php`, `..._create_enrollments_table.php`, `..._create_grades_table.php` |
| S4-02 | `config/diem.php` + `DiemService` | BE | 5 h | `config/diem.php`, `app/Services/DiemService.php` |
| S4-03 | Unit test `DiemServiceTest` (vector điểm) | Test | 4 h | `tests/Unit/DiemServiceTest.php` |
| S4-04 | Model + quan hệ + `CoursePolicy` + `GradeObserver` | BE | 6 h | `app/Models/{Course,Enrollment,Grade}.php`, `app/Policies/CoursePolicy.php` |
| S4-05 | `CourseController` CRUD + `show` + chốt/mở lớp HP | BE | 10 h | `app/Http/Controllers/CourseController.php`, `app/Http/Requests/CourseRequest.php` |
| S4-06 | Ghi danh SV vào lớp HP (thêm nhiều + rút) | BE | 8 h | `app/Http/Controllers/EnrollmentController.php` |
| S4-07 | Nhập điểm hàng loạt theo lớp (bulk) | BE | 10 h | `app/Http/Controllers/GradeController.php`, `app/Http/Requests/GradeBulkRequest.php` |
| S4-08 | Views: courses (index/create/edit/show), enrollments, grades/edit | FE | 18 h | `resources/views/courses/*`, `resources/views/grades/edit.blade.php` |
| S4-09 | Route nhóm `lop-hoc-phan` + menu theo vai trò | BE+FE | 3 h | `routes/web.php`, `partials/sidebar.blade.php` |
| S4-10 | Feature test: quyền GV, ghi danh, nhập điểm, chốt điểm | Test | 15 h | `tests/Feature/Diem/*` |

### 7.2. Lược đồ 3 bảng mới (S4-01)

**`courses` — lớp học phần**

| Cột | Kiểu | Ghi chú |
|---|---|---|
| `id` | PK | |
| `ma_lhp` | string(30) unique | VD `CT101-HK1-2026-N01` |
| `subject_id` | FK subjects `restrictOnDelete` | |
| `semester_id` | FK semesters `restrictOnDelete` | |
| `giang_vien_id` | FK users, nullable, `nullOnDelete` | Validate: user có `role = giang_vien` |
| `phong_hoc` / `lich_hoc` | string, nullable | VD "A2-301", "Thứ 2, 1–3" |
| `so_sv_toi_da` | unsignedSmallInteger default 50 | |
| `trang_thai` | string(20) default `mo` | `mo` / `dong` / `ket_thuc` |
| `ghi_chu` | text nullable | |
| index | (`semester_id`, `subject_id`), (`giang_vien_id`) | |

**`enrollments` — ghi danh**

| Cột | Kiểu | Ghi chú |
|---|---|---|
| `id` | PK | |
| `student_id` | FK students `cascadeOnDelete` | |
| `course_id` | FK courses `cascadeOnDelete` | |
| `trang_thai` | string(20) default `dang_hoc` | `dang_hoc`/`du_thi`/`hoc_lai`/`rut` |
| `lan_hoc` | unsignedTinyInteger default 1 | Học lại ⇒ tăng lên |
| unique | (`student_id`, `course_id`, `lan_hoc`) | Chống ghi danh trùng |
| index | (`course_id`, `trang_thai`) | |

**`grades` — điểm**

| Cột | Kiểu | Ghi chú |
|---|---|---|
| `id` | PK | |
| `enrollment_id` | FK enrollments `cascadeOnDelete`, **unique** | 1 ghi danh ⇔ 1 bộ điểm |
| `diem_qua_trinh` / `diem_thi` | decimal(4,2) nullable | 0.00 – 10.00 |
| `diem_tb` | decimal(4,2) nullable | Do hệ thống tính |
| `diem_chu` | string(3) nullable | A, B+, … F |
| `diem_he4` | decimal(3,2) nullable | 0.00 – 4.00 |
| `dat` | boolean nullable | `diem_tb >= 4.0` |
| `nguoi_nhap_id` | FK users `nullOnDelete` | Vết kiểm toán |
| `nhap_luc` | timestamp nullable | Lần nhập gần nhất |
| `khoa_luc` | timestamp nullable | **Có giá trị ⇒ không cho sửa** (ADR-11) |
| `ghi_chu` | text nullable | |

### 7.3. `DiemService` — trái tim nghiệp vụ (S4-02)

```php
final class DiemService
{
    /** @return array{diem_tb: ?float, diem_chu: ?string, diem_he4: ?float, dat: ?bool} */
    public function tinh(?float $quaTrinh, ?float $thi): array
    {
        if ($quaTrinh === null || $thi === null) {
            return ['diem_tb' => null, 'diem_chu' => null, 'diem_he4' => null, 'dat' => null];
        }

        $tb  = round($quaTrinh * config('diem.trong_so_qua_trinh')
                 + $thi * config('diem.trong_so_thi'), 2);
        $chu = $this->quyDoiChu($tb);

        return [
            'diem_tb'  => $tb,
            'diem_chu' => $chu,
            'diem_he4' => config("diem.he4.$chu"),
            'dat'      => $tb >= config('diem.diem_dat'),
        ];
    }

    private function quyDoiChu(float $tb): string
    {
        foreach (config('diem.thang_diem_chu') as $chu => $moc) { // ['A' => 8.5, 'B+' => 8.0, ...]
            if ($tb >= $moc) {
                return $chu;
            }
        }

        return 'F';
    }
}
```

**Bộ dữ liệu test bắt buộc (`DiemServiceTest`):**

| QT | Thi | TB kỳ vọng | Chữ | Hệ 4 | Đạt |
|---|---|---|---|---|---|
| 8.5 | 9.0 | 8.80 | A | 4.0 | true |
| 8.0 | 8.0 | 8.00 | B+ | 3.5 | true |
| 7.0 | 7.0 | 7.00 | B | 3.0 | true |
| 6.0 | 5.0 | 5.40 | D+ | 1.5 | true |
| 3.0 | 2.0 | 2.40 | F | 0.0 | false |
| 5.0 | null | null | null | null | null |
| 9.95 | 9.95 | 9.95 | A | 4.0 | true |

### 7.4. Route nhóm `lop-hoc-phan` (S4-09)

```php
Route::middleware('auth')->group(function () {
    // Admin + Giáo vụ: quản lý lớp HP & ghi danh
    Route::middleware('role:admin,giao_vu')->group(function () {
        Route::resource('lop-hoc-phan', CourseController::class)
            ->parameters(['lop-hoc-phan' => 'course'])->except('show');
        Route::post('lop-hoc-phan/{course}/ghi-danh', [EnrollmentController::class, 'store'])->name('lop-hoc-phan.ghi-danh');
        Route::delete('lop-hoc-phan/{course}/ghi-danh/{enrollment}', [EnrollmentController::class, 'destroy'])->name('lop-hoc-phan.rut');
    });

    Route::get('lop-hoc-phan/{course}', [CourseController::class, 'show'])->name('lop-hoc-phan.show');

    // Giảng viên + admin: nhập điểm cho lớp mình dạy (CoursePolicy kiểm tra quyền sở hữu)
    Route::middleware('role:admin,giang_vien')->group(function () {
        Route::get('lop-hoc-phan/{course}/nhap-diem', [GradeController::class, 'edit'])->name('diem.nhap');
        Route::patch('lop-hoc-phan/{course}/nhap-diem', [GradeController::class, 'update'])->name('diem.luu');
        Route::post('lop-hoc-phan/{course}/chot-diem', [GradeController::class, 'lock'])->name('diem.chot');
        Route::post('lop-hoc-phan/{course}/mo-khoa', [GradeController::class, 'unlock'])->name('diem.mo-khoa');
    });
});
```

### 7.5. `CoursePolicy` (S4-04) — chống IDOR

```php
public function update(User $user, Course $course): bool
{
    return match (true) {
        $user->role === Role::ADMIN => true,
        $user->role === Role::GIANG_VIEN => $course->giang_vien_id === $user->id,
        default => false,
    };
}

public function nhapDiem(User $user, Course $course): bool
{
    return $this->update($user, $course) && $course->trang_thai !== 'ket_thuc';
}
```
Trong Controller luôn gọi `$this->authorize('nhapDiem', $course);` **trước** khi xử lý → GV A không thể nhập điểm lớp của GV B.

### 7.6. Nghiệp vụ nhập điểm (S4-07)

1. Form nhập là **ma trận**: mỗi dòng = 1 SV (mã SV, họ tên), cột `diem_qua_trinh`, `diem_thi`, cột đọc-only `TB / chữ / hệ 4`.
2. `GradeBulkRequest`:
   ```php
   'diem'                     => ['required', 'array'],
   'diem.*.enrollment_id'     => ['required', 'exists:enrollments,id'],
   'diem.*.diem_qua_trinh'    => ['nullable', 'numeric', 'between:0,10'],
   'diem.*.diem_thi'          => ['nullable', 'numeric', 'between:0,10'],
   ```
3. Controller: `DB::transaction` + `firstOrCreate` bản ghi `grades` theo `enrollment_id` → gọi `DiemService::tinh()` → lưu `diem_tb/diem_chu/diem_he4/dat`, `nguoi_nhap_id`, `nhap_luc`.
4. Bỏ trống cả 2 cột điểm ⇒ **không lưu** bản ghi đó (giữ `null`), không đánh dấu là 0.
5. **Chốt điểm:** `POST chot-diem` ⇒ gán `khoa_luc = now()` cho mọi bản ghi có điểm của lớp + đổi `courses.trang_thai = 'ket_thuc'`; từ đây `update()` trả về lỗi *"Điểm đã chốt, không thể sửa"* (trừ admin có nút "Mở khoá" ghi log).
6. Làm tròn 2 chữ số thập phân (`round($x, 2)`) — lưu ý PHP `round(8.615, 2) = 8.62`.

### 7.7. Acceptance Criteria

- **AC4-1:** GV nhập QT=7.5, Thi=8.0 ⇒ TB = **7.80**, chữ **B**, hệ 4 **3.0**, Đạt = *Đạt* (khớp bảng quy đổi ở mục 3.2).
- **AC4-2:** GV B truy cập `/lop-hoc-phan/{lớp của GV A}/nhap-diem` ⇒ **403**.
- **AC4-3:** Nhập điểm 10.5 hoặc `-1` ⇒ lỗi validate "Điểm phải nằm trong khoảng 0 – 10".
- **AC4-4:** Sau khi **Chốt điểm**, mọi thao tác sửa ⇒ lỗi *"Điểm đã chốt, không thể sửa"*; form chuyển chế độ chỉ-đọc kèm badge "Đã chốt ngày …".
- **AC4-5:** Không thể ghi danh 1 SV vào cùng lớp HP 2 lần (unique + thông báo tiếng Việt); không thể ghi danh vượt `so_sv_toi_da`.
- **AC4-6:** Lớp HP có sinh viên đã có điểm ⇒ **không** xoá được lớp HP (thông báo chặn).
- **AC4-7:** Danh sách lớp HP của GV chỉ hiện lớp mình dạy (+ trạng thái nhập điểm: "Chưa nhập / Nhập 1 phần / Đã chốt").

### 7.8. Test cần viết (S4-10)

| File test | Nội dung |
|---|---|
| `tests/Unit/DiemServiceTest.php` | 7 vector ở bảng 7.3 + test đọc trọng số từ `config('diem.*')` |
| `tests/Feature/Diem/CourseCrudTest.php` | tạo lớp HP (validate `ma_lhp` unique, `giang_vien_id` phải là giảng viên); chặn xoá lớp đã có điểm |
| `tests/Feature/Diem/CoursePolicyTest.php` | GV A không xem/nhập điểm lớp GV B (403); admin làm được |
| `tests/Feature/Diem/EnrollmentTest.php` | ghi danh trùng → lỗi; vượt sĩ số → lỗi; rút SV đã có điểm → lỗi |
| `tests/Feature/Diem/GradeEntryTest.php` | nhập bulk ⇒ `assertDatabaseHas` với `diem_tb`, `diem_chu`, `diem_he4`, `dat` đúng; bỏ trống 2 điểm ⇒ không có bản ghi |
| `tests/Feature/Diem/GradeLockTest.php` | sau chốt ⇒ `update` trả 403/redirect kèm lỗi; mở khoá bởi admin ⇒ sửa lại được |

### 7.9. Kịch bản demo (12 phút)

1. Giáo vụ tạo lớp HP *Lập trình Web – HK1 2026-2027 – GV Nguyễn Văn A*; ghi danh 10 SV (thử thêm trùng 1 SV để thấy chặn).
2. Đăng nhập GV A → thấy lớp mình dạy → nhập điểm 10 SV (có 1 SV điểm 3.0/2.0 = F).
3. Bấm **Lưu** → bảng hiện TB/chữ/hệ 4 ngay; thử nhập 11 điểm → thấy lỗi validate.
4. Bấm **Chốt điểm** → thử sửa lại → thấy thông báo chặn.
5. Đăng nhập GV B → mở đúng URL lớp của GV A → 403.
6. Chạy `php artisan test --filter=Diem` → xanh.

### 7.10. Rủi ro & xử lý

| Rủi ro | Mức | Xử lý |
|---|---|---|
| Sai/đổi thang điểm so với quy chế trường | Cao | Tập trung mọi công thức vào `config/diem.php` + `DiemService`; đổi 1 chỗ là toàn hệ thống theo (chốt với GV trước khi code — ADR-04) |
| Làm tròn sai (sai số dấu phẩy động) | TB | Luôn `round($x, 2)` và lưu `decimal(4,2)`; test vector đã bao phủ |
| Nhập điểm chồng chéo nhiều GV | TB | Policy theo `giang_vien_id`; ghi `nguoi_nhap_id` + `nhap_luc` để truy vết |
| Mất điểm khi transaction lỗi giữa dòng | TB | Toàn bộ bulk trong 1 `DB::transaction`; validate trước khi ghi |
| SV rút lớp nhưng vẫn còn điểm | TB | Chặn rút khi đã có điểm; nếu cần → chuyển sang trạng thái `rut` thay vì xoá (giữ vết) |

---

## 8. SPRINT 5 — Sinh viên tra cứu điểm · Dashboard thống kê (2 tuần · ~69 h)

**Sprint Goal:** *"Sinh viên tự xem/in được bảng điểm & GPA của mình; Ban giám hiệu/giáo vụ thấy dashboard trực quan thay cho việc đếm tay trên Excel."*

### 8.1. Sprint Backlog

| ID | Task | Loại | Ước lượng | File chính |
|---|---|---|---|---|
| S5-01 | `GpaService`: GPA học kỳ, GPA tích luỹ, tín chỉ đạt, xếp loại | BE | 6 h | `app/Services/GpaService.php` |
| S5-02 | Unit test `GpaServiceTest` | Test | 4 h | `tests/Unit/GpaServiceTest.php` |
| S5-03 | Cổng sinh viên: bảng điểm theo học kỳ + in | BE+FE | 8 h | `app/Http/Controllers/SinhVien/BangDiemController.php`, `resources/views/sinh-vien/bang-diem/{index,in}.blade.php` |
| S5-04 | Trang chi tiết SV (giáo vụ xem bảng điểm 1 SV) | BE+FE | 4 h | `StudentController@show` + `resources/views/students/show.blade.php` |
| S5-05 | `ThongKeService` + cache 5 phút | BE | 9 h | `app/Services/ThongKeService.php` |
| S5-06 | Dashboard admin/giáo vụ (4 card + 4 biểu đồ) | BE+FE | 10 h | `app/Http/Controllers/DashboardController.php`, `resources/views/dashboard/*` |
| S5-07 | Dashboard giảng viên & sinh viên | BE+FE | 6 h | `resources/views/dashboard/giang-vien.blade.php`, `dashboard/sinh-vien.blade.php` |
| S5-08 | Cài Chart.js + entry Vite + script vẽ biểu đồ | FE | 8 h | `npm i chart.js`, `resources/js/dashboard.js`, `vite.config.js` |
| S5-09 | Tối ưu query & index bổ sung | BE | 6 h | migration `add_indexes_for_reporting_table.php`, eager loading toàn bộ màn danh sách |
| S5-10 | Feature/Unit test dashboards & quyền xem điểm | Test | 8 h | `tests/Feature/Dashboard/*`, `tests/Feature/SinhVien/BangDiemTest.php` |

### 8.2. Quy tắc tính GPA (S5-01) — chốt rõ để tránh tranh cãi

```
GPA học kỳ  = Σ(điểm_hệ4 của môn × số_tín_chỉ của môn) / Σ(số_tín_chỉ các môn có điểm trong kỳ)
GPA tích lũy = tính trên TẤT CẢ môn đã có điểm (mọi học kỳ)
Tín chỉ đạt = Σ tín chỉ các môn có dat = true
Xếp loại    = Xuất sắc ≥ 3.60 · Giỏi ≥ 3.20 · Khá ≥ 2.50 · Trung bình ≥ 2.00 · Yếu < 2.00
Môn học lại = lấy điểm CAO NHẤT trong các lần học (theo lan_hoc)
```

Bộ dữ liệu test `GpaServiceTest` (giả định): 3 môn – LTW 3TC (3.5) + CSDL 3TC (3.0) + Toán 2TC (0.0, F)
⇒ GPA kỳ = (3.5×3 + 3.0×3 + 0×2) / 8 = **2.4375 ≈ 2.44**, tín chỉ đạt = **6/8**, xếp loại **Trung bình**.

### 8.3. Dashboard – chỉ số & biểu đồ (S5-05, S5-06)

| Vị trí | Nội dung | Nguồn dữ liệu |
|---|---|---|
| Card 1–4 | Tổng SV đang học · Tổng lớp HP đang mở · Tổng môn học · Tổng giảng viên | `count()` với điều kiện |
| Biểu đồ cột | Số SV theo Khoa | `students ⋈ classes ⋈ majors ⋈ faculties`, `groupBy` |
| Biểu đồ tròn | Tỉ lệ SV theo Ngành (top 6) | `groupBy` + `limit` |
| Biểu đồ cột ghép | Số SV **Đạt / Không đạt** theo môn học (top 8 môn có nhiều SV nhất) | `grades.dat` + `enrollments` + `subjects` |
| Biểu đồ đường | Điểm TB toàn trường theo học kỳ | `AVG(grades.diem_tb)` group theo `semesters` |
| Bảng phụ | 10 SV có GPA cao nhất (nếu kịp) | `GpaService` + cache |

**Cách truyền dữ liệu an toàn ra JS (không dùng `@json` lẫn trong HTML thủ công):**
```blade
<canvas id="svTheoKhoa"
    data-nhan='@json($thongKe["nhanKhoa"])'
    data-gia-tri='@json($thongKe["soSvKhoa"])'></canvas>
```
```js
// resources/js/dashboard.js
import Chart from 'chart.js/auto';
document.querySelectorAll('canvas[data-nhan]').forEach((el) => {
    new Chart(el, {
        type: el.dataset.loai ?? 'bar',
        data: {
            labels: JSON.parse(el.dataset.nhan),
            datasets: [{ label: 'Số sinh viên', data: JSON.parse(el.dataset.giaTri) }],
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } },
    });
});
```
> Nhớ thêm `resources/js/dashboard.js` vào `input` của `vite.config.js` và dùng `@vite(['resources/js/dashboard.js'])` ở layout dashboard.

### 8.4. Cache & tối ưu (S5-05, S5-09)

```php
return Cache::remember('dashboard.thong-ke.'.today()->format('Ymd'), now()->addMinutes(5),
    fn () => [ /* mảng số liệu đã tính sẵn bằng SQL aggregate */ ]);
```
- Invalidate đơn giản: cache key chứa ngày; nút **"Làm mới số liệu"** cho admin ⇒ `Cache::forget(...)`.
- Index bổ sung: `students.lop_id`, `enrollments.course_id`, `enrollments.student_id`, `grades.enrollment_id`, `courses.semester_id`, `courses.giang_vien_id`.
- Kiểm tra N+1: bật `laravel/pail` / `DB::listen` đếm số query; mục tiêu **< 15 query** cho trang dashboard, **< 10 query** cho trang bảng điểm sinh viên.

### 8.5. Route Sprint 5

```php
Route::middleware(['auth', 'role:sinh_vien'])->prefix('sinh-vien-cua-toi')->name('sv.')->group(function () {
    Route::get('bang-diem', [BangDiemController::class, 'index'])->name('bang-diem');
    Route::get('bang-diem/in', [BangDiemController::class, 'in'])->name('bang-diem.in');
});

// Tra cứu công khai (Should-have, có rate limit)
Route::middleware('throttle:10,1')->group(function () {
    Route::get('/tra-cuu-diem', [TraCuuDiemController::class, 'create'])->name('tra-cuu.create');
    Route::post('/tra-cuu-diem', [TraCuuDiemController::class, 'store'])->name('tra-cuu.store');
});
```

### 8.6. Acceptance Criteria

- **AC5-1:** SV A xem `/sinh-vien-cua-toi/bang-diem` ⇒ **chỉ** thấy điểm của mình; query luôn theo `auth()->user()->student`, **không** nhận `student_id` từ request (chống IDOR).
- **AC5-2:** Bảng điểm hiển thị theo từng học kỳ + dòng tổng kết: tín chỉ đăng ký, tín chỉ đạt, GPA kỳ, GPA tích luỹ, xếp loại — số liệu khớp tính tay ở 8.2.
- **AC5-3:** Trang in bảng điểm (`Ctrl+P`) hiển thị đúng, không có sidebar/menu, không cắt cột (kiểm tra học kỳ 10 môn).
- **AC5-4:** Dashboard admin hiển thị 4 card + 4 biểu đồ; số trên card khớp `SELECT COUNT(*)`; khi dữ liệu rỗng biểu đồ vẫn không lỗi JS (hiện "Chưa có dữ liệu").
- **AC5-5:** Dashboard tải **< 1,5 giây** với dữ liệu seeder (50 SV, 5 lớp HP, 200 dòng điểm) đo bằng tab Network.
- **AC5-6:** Dashboard giảng viên hiện danh sách lớp mình dạy + số SV + cảnh báo "Chưa nhập điểm" cho lớp chưa có dòng điểm nào.
- **AC5-7:** Tra cứu công khai yêu cầu đúng cặp `ma_sv + ngay_sinh`; sai ⇒ thông báo chung chung, không tiết lộ mã nào tồn tại.

### 8.7. Test cần viết (S5-10)

| File test | Nội dung |
|---|---|
| `tests/Unit/GpaServiceTest.php` | vector ở 8.2; trường hợp chưa có điểm (GPA = 0/null); môn học lại lấy điểm cao nhất |
| `tests/Feature/SinhVien/BangDiemTest.php` | SV chỉ thấy điểm của mình (`assertSee`/`assertDontSee` mã SV khác); tài khoản không gắn hồ sơ ⇒ thông báo "Chưa có hồ sơ sinh viên" |
| `tests/Feature/Dashboard/DashboardAccessTest.php` | 4 vai trò đều 200 nhưng nội dung khác nhau; khách ⇒ redirect login |
| `tests/Feature/Dashboard/ThongKeServiceTest.php` | dữ liệu seeder cố định: số SV theo khoa, số đạt/không đạt khớp kỳ vọng |
| `tests/Feature/TraCuuDiemTest.php` | đúng cặp ⇒ 200 có điểm; sai ⇒ không lộ dữ liệu; quá 10 request/phút ⇒ 429 |

### 8.8. Kịch bản demo (10 phút)

1. Đăng nhập SV → *Bảng điểm của tôi* → GPA kỳ/tích luỹ, tín chỉ đạt, xếp loại; đổi học kỳ bằng dropdown.
2. Bấm *In bảng điểm* → Ctrl+P → xem trước bản in.
3. Cửa sổ ẩn danh → đăng nhập admin → dashboard: hover biểu đồ, bấm "Làm mới số liệu".
4. Đăng nhập GV → dashboard GV: thấy lớp chưa nhập điểm.
5. Chạy `php artisan test --filter=Gpa` → xanh.

### 8.9. Rủi ro & xử lý

| Rủi ro | Mức | Xử lý |
|---|---|---|
| SV xem được điểm của SV khác (IDOR) | Cao | Không nhận `student_id` từ request; Feature test `assertDontSee` |
| Biểu đồ không render do asset cũ | TB | DoD: build lại asset sau khi thêm entry mới (`npm run build`) |
| Dashboard chậm khi dữ liệu lớn | TB | Aggregate bằng SQL + cache 5 phút + index (S5-09) |
| Công thức GPA khác quy chế | TB | Chốt công thức ở 8.2 với giáo vụ; logic tập trung ở `GpaService` |

---

## 9. SPRINT 6 — Báo cáo · Thông báo · Hardening · Bàn giao (2 tuần · ~105 h)

**Sprint Goal:** *"Có báo cáo Excel/PDF chuẩn, thông báo nội bộ hoạt động, hệ thống được kiểm thử & tối ưu, tài liệu + kịch bản bảo vệ sẵn sàng."*

### 9.1. Sprint Backlog

| ID | Task | Loại | Ước lượng | File chính |
|---|---|---|---|---|
| S6-01 | Export Excel danh sách SV (theo bộ lọc hiện tại) | BE | 8 h | `app/Exports/StudentsExport.php`, `StudentController@export` |
| S6-02 | Export Excel bảng điểm lớp HP + bảng điểm cá nhân | BE | 6 h | `app/Exports/{GradesExport,BangDiemCaNhanExport}.php` |
| S6-03 | Cài DomPDF + cấu hình font tiếng Việt | Infra | 3 h | `composer require barryvdh/laravel-dompdf`, `config/dompdf.php` |
| S6-04 | PDF: bảng điểm lớp HP, bảng điểm cá nhân, danh sách SV | BE+FE | 10 h | `resources/views/reports/pdf/*.blade.php`, `app/Http/Controllers/ReportController.php` |
| S6-05 | Thông báo: migration + model + gửi theo đối tượng | DB+BE | 10 h | `..._create_announcements_table.php`, `..._create_announcement_user_table.php`, `app/Services/ThongBaoService.php` |
| S6-06 | Thông báo: màn quản lý + hộp thư SV + badge chưa đọc | BE+FE | 12 h | `app/Http/Controllers/AnnouncementController.php`, `resources/views/thong-bao/*` |
| S6-07 | Kiểm thử toàn hệ thống (UAT 4 vai trò + responsive + bảo mật) | Test | 12 h | `docs/uat-checklist.md` |
| S6-08 | Hardening & tối ưu cuối (cache, index, lint, log) | BE | 8 h | `php artisan optimize`, `vendor/bin/pint`, `storage/logs` |
| S6-09 | Dữ liệu demo + script backup | DB | 6 h | `database/seeders/DemoSeeder.php`, `docs/backup-db.bat` |
| S6-10 | Tài liệu bàn giao (README, đặc tả, ERD, hướng dẫn, nhật ký sprint) | Docs | 12 h | `README.md`, `docs/01..04`, `docs/screenshots/` |
| S6-11 | Slide + kịch bản bảo vệ + buổi Review/Retro cuối | Docs | 8 h | `docs/slide-bao-ve.md` |
| S6-12 | Buffer fix bug phát sinh từ UAT | Fix | 10 h | – |

### 9.2. Báo cáo (S6-01 → S6-04)

| Báo cáo | Định dạng | Nội dung | Route |
|---|---|---|---|
| Danh sách sinh viên (theo bộ lọc) | Excel | 12 cột: Mã, Họ tên, Ngày sinh, Giới tính, Lớp, Ngành, Khoa, Email, SĐT, Trạng thái, Ngày nhập học, Ghi chú | `GET /bao-cao/sinh-vien/excel` (+ giữ query filter) |
| Danh sách sinh viên | PDF | Header trường/khoa, ngày in, người lập, ô ký | `GET /bao-cao/sinh-vien/pdf` |
| Bảng điểm lớp HP | Excel + PDF | Mã SV, Họ tên, QT, Thi, TB, Chữ, Hệ 4, Đạt; cuối bảng: số đạt/không đạt, TB lớp | `GET /bao-cao/lop-hoc-phan/{course}/excel\|pdf` |
| Bảng điểm cá nhân | PDF | Theo học kỳ + GPA kỳ/tích luỹ/xếp loại | `GET /sinh-vien-cua-toi/bang-diem/in` (S5) |

**Lưu ý kỹ thuật:**
- PDF **không chạy được** `@vite`/Tailwind ⇒ viết CSS thuần trong `<style>` của view PDF.
- Bắt buộc cấu hình font tiếng Việt trong `config/dompdf.php`:
  ```php
  'default_font' => 'DejaVu Sans',
  'dpi'          => 130,
  'enable_php'   => false,
  'font_dir'     => storage_path('fonts'),
  ```
- Tên file xuất: `danh-sach-sinh-vien-YYYYMMDD-HHmm.xlsx`, `bang-diem-{ma_lhp}.pdf`.
- Xuất theo bộ lọc hiện tại ⇒ truyền `request()->query()` vào export (`FromQuery`) để nút "Xuất Excel" cho ra đúng dữ liệu đang xem.

### 9.3. Thông báo (S6-05, S6-06)

**`announcements`:** id, `tieu_de`(200), `noi_dung`(longText), `nguoi_gui_id` FK users, `doi_tuong` (`toan_truong`/`khoa`/`lop`/`hoc_phan`), `faculty_id`?, `lop_id`?, `course_id`?, `published_at`?, timestamps.
**`announcement_user`:** `announcement_id` + `user_id` (PK tổ hợp), `da_doc` boolean default false, `doc_luc`?.

```php
// ThongBaoService::gui(Announcement $a): int
$userIds = match ($a->doi_tuong) {
    'toan_truong' => User::pluck('id'),
    'khoa'        => Student::whereHas('schoolClass.major',
                        fn ($q) => $q->where('faculty_id', $a->faculty_id))
                        ->whereNotNull('user_id')->pluck('user_id'),
    'lop'         => Student::where('lop_id', $a->lop_id)->whereNotNull('user_id')->pluck('user_id'),
    'hoc_phan'    => Enrollment::where('course_id', $a->course_id)->with('student')->get()
                        ->pluck('student.user_id')->filter(),
};

$a->nguoiNhan()->syncWithoutDetaching(
    $userIds->mapWithKeys(fn ($id) => [$id => ['da_doc' => false]])->all()
);

return $userIds->count();
```
- Badge số chưa đọc trên topbar: `auth()->user()->announcements()->wherePivot('da_doc', false)->count()` (cache 60 giây).
- SV mở thông báo ⇒ `da_doc = true`, `doc_luc = now()`.
- Quyền: admin/giáo vụ tạo – đăng – huỷ; giảng viên gửi được cho lớp HP mình dạy (Should-have).

### 9.4. Route Sprint 6

```php
Route::middleware(['auth', 'role:admin,giao_vu'])->group(function () {
    Route::get('bao-cao/sinh-vien/excel', [ReportController::class, 'sinhVienExcel'])->name('bao-cao.sv.excel');
    Route::get('bao-cao/sinh-vien/pdf', [ReportController::class, 'sinhVienPdf'])->name('bao-cao.sv.pdf');
    Route::get('bao-cao/lop-hoc-phan/{course}/excel', [ReportController::class, 'bangDiemExcel'])->name('bao-cao.diem.excel');
    Route::get('bao-cao/lop-hoc-phan/{course}/pdf', [ReportController::class, 'bangDiemPdf'])->name('bao-cao.diem.pdf');
    Route::resource('thong-bao', AnnouncementController::class)->except('show');
    Route::post('thong-bao/{announcement}/dang', [AnnouncementController::class, 'publish'])->name('thong-bao.dang');
});

Route::middleware('auth')->group(function () {
    Route::get('hop-thu', [HopThuController::class, 'index'])->name('hop-thu.index');
    Route::get('hop-thu/{announcement}', [HopThuController::class, 'show'])->name('hop-thu.show');
    Route::patch('hop-thu/{announcement}/da-doc', [HopThuController::class, 'markRead'])->name('hop-thu.da-doc');
});
```

### 9.5. Hardening & UAT (S6-07, S6-08)

**Checklist bảo mật (bắt buộc kiểm tra từng mục):**
- [ ] Mọi route nghiệp vụ nằm trong `['auth','role:...']`; rà lại bằng `php artisan route:list`.
- [ ] Mọi form có `@csrf`; thử POST thiếu token ⇒ **419**.
- [ ] Không có `{!! !!}` với dữ liệu người dùng nhập (XSS) — nội dung thông báo dùng `nl2br(e($text))`.
- [ ] Truy vấn dùng Eloquent/Query Builder (không nối chuỗi SQL).
- [ ] Upload: ảnh `jpg/jpeg/png/webp ≤ 2MB`, Excel `≤ 5MB`; tên file do hệ thống sinh.
- [ ] Kiểm tra IDOR: SV ↔ SV, GV ↔ GV, giáo vụ ↔ admin (`assertForbidden`).
- [ ] `APP_DEBUG=false` khi demo/bàn giao; trang 500 không lộ stack trace.
- [ ] Mật khẩu hash BCrypt; tài khoản mới phải được nhắc đổi mật khẩu.

**Checklist hiệu năng:**
- [ ] `php artisan optimize` chạy thành công.
- [ ] Trang danh sách SV 500 bản ghi: **< 1 giây**, không N+1 (≤ 10 query).
- [ ] Index đủ cho các cột lọc (S5-09).

**Checklist responsive:** 375px (điện thoại) · 768px (tablet) · 1440px (desktop): bảng cuộn ngang, sidebar thu gọn, nút không tràn dòng.

**UAT – kịch bản 4 vai trò (ghi kết quả vào `docs/uat-checklist.md`):**

| # | Vai trò | Kịch bản | Kết quả |
|---|---|---|---|
| 1 | Admin | Tạo tài khoản giảng viên → gán vào 1 lớp HP → đăng nhập tài khoản đó nhập điểm | ☐ |
| 2 | Giáo vụ | Tạo khoa/ngành/lớp/môn/học kỳ → tạo 1 SV có ảnh → import 10 SV → tạo lớp HP → ghi danh → xuất Excel | ☐ |
| 3 | Giảng viên | Nhập điểm lớp mình dạy → chốt điểm → xuất PDF → kiểm tra không sửa được sau chốt | ☐ |
| 4 | Sinh viên | Đăng nhập → xem bảng điểm + GPA → in → đọc thông báo mới → không vào được khu quản trị | ☐ |

### 9.6. Dữ liệu demo & tài liệu bàn giao (S6-09 → S6-11)

```bat
:: Tạo lại toàn bộ dữ liệu demo bằng 1 lệnh
php artisan migrate:fresh --seed

:: Backup DB (nội dung docs/backup-db.bat)
"%LARAGON%\bin\mysql\mysql-8.4\bin\mysqldump" -u root qlsv > docs\backup\qlsv_%date:~-4%%date:~3,2%%date:~0,2%.sql
```

**Danh mục tài liệu bàn giao:**

| File | Nội dung |
|---|---|
| `README.md` | Giới thiệu, ảnh chụp, **5 bước cài đặt**, tài khoản demo, lệnh thường dùng, phân quyền |
| `docs/00-tong-quan.md` | Mục tiêu, phạm vi, kiến trúc, môi trường (bảng ở mục 0) |
| `docs/01-dac-ta-chuc-nang.md` | Mỗi chức năng: mô tả – vai trò – dữ liệu vào/ra – validate – AC |
| `docs/02-so-do-erd.md` | Sơ đồ ERD bằng **Mermaid** + giải thích quan hệ 1–n, n–n |
| `docs/03-huong-dan-su-dung.md` | Hướng dẫn theo từng vai trò kèm ảnh chụp màn hình |
| `docs/04-nhat-ky-sprint.md` | Mỗi sprint: mục tiêu – đã làm – chưa làm – retro |
| `docs/uat-checklist.md` | Bảng UAT ở 9.5 đã điền kết quả |
| `docs/slide-bao-ve.md` | Dàn ý 12 slide + kịch bản demo 7 phút + Q&A dự kiến |
| `docs/screenshots/` | Ảnh chụp 4 vai trò, dashboard, báo cáo |

**Dàn ý slide bảo vệ:** 1) Đặt vấn đề · 2) Mục tiêu & phạm vi · 3) Kiến trúc & công nghệ · 4) Mô hình dữ liệu (ERD) · 5) Phân quyền · 6) Quản lý sinh viên & import Excel · 7) Nhập điểm & công thức tính · 8) Tra cứu điểm – GPA · 9) Dashboard – báo cáo · 10) Kiểm thử & bảo mật · 11) Demo trực tiếp · 12) Hạn chế & hướng phát triển.

### 9.7. Acceptance Criteria

- **AC6-1:** File Excel xuất ra mở được bằng Excel, tiếng Việt **không lỗi font**, đủ 12 cột, đúng theo bộ lọc đang xem.
- **AC6-2:** PDF bảng điểm đúng dấu tiếng Việt; bảng 50 dòng không tràn trang (tự ngắt trang, lặp header ở trang 2).
- **AC6-3:** Thông báo gửi cho "Lớp CNTT-K23-A" ⇒ **chỉ** SV lớp đó thấy; badge chưa đọc đúng; mở thông báo ⇒ badge giảm sau khi tải lại.
- **AC6-4:** `php artisan migrate:fresh --seed` tạo môi trường demo hoàn chỉnh trong **< 60 giây**, không lỗi.
- **AC6-5:** `php artisan test` **100% pass**; `vendor/bin/pint --test` pass; `storage/logs/laravel.log` không có ERROR sau UAT.
- **AC6-6:** Toàn bộ checklist bảo mật + UAT ở 9.5 đã được kiểm tra và đánh dấu.

### 9.8. Kịch bản demo cuối (12–15 phút)

1. `php artisan migrate:fresh --seed` → mở trang chủ → đăng nhập admin → dashboard có số liệu.
2. Tạo nhanh 1 SV → import 10 SV → xuất Excel danh sách.
3. Giáo vụ tạo lớp HP + ghi danh → GV nhập điểm → chốt điểm → xuất PDF bảng điểm lớp.
4. SV xem bảng điểm + GPA + in; đọc thông báo mới.
5. Chỉ ra **403** khi SV truy cập `/sinh-vien`.
6. Chạy `php artisan test` → show toàn bộ xanh.
7. Trình bày slide về hạn chế & hướng phát triển.

### 9.9. Rủi ro & xử lý

| Rủi ro | Mức | Xử lý |
|---|---|---|
| PDF lỗi font tiếng Việt | Cao | Dùng `DejaVu Sans`; test ngay khi làm S6-04 (đừng để tới ngày cuối) |
| Package Excel/DomPDF không tương thích Laravel 13 | Cao | Cài & test ngay **đầu sprint**; phương án B: CSV (`fputcsv`) + in từ trình duyệt |
| Hết thời gian do bug UAT | Cao | Dành sẵn 10 h buffer (S6-12); ưu tiên bug chặn demo |
| Quên build asset trước khi demo | TB | Đưa `npm run build` vào checklist trước demo |
| Mất dữ liệu khi demo | TB | Backup DB trước demo + bộ seeder demo |

---

## 10. Tổng hợp ước lượng & năng lực thực hiện

| Sprint | Ước lượng | Số ngày công (6 h/ngày) | So với trần 10 ngày/2 tuần |
|---|---|---|---|
| Sprint 0 | 36,5 h | 6,1 | Vượt 1,1 ngày (tuần setup ⇒ chấp nhận, có thể cắt S0-14) |
| Sprint 1 | 68 h | 11,3 | **Vượt 1,3 ngày** ⇒ cắt bớt phần show hồ sơ |
| Sprint 2 | 73 h | 12,2 | **Vượt 2,2 ngày** ⇒ gộp view create/edit, cắt `show` của Khoa |
| Sprint 3 | 78 h | 13,0 | **Vượt 3,0 ngày** ⇒ bỏ preview ảnh, bỏ import gán SV hàng loạt |
| Sprint 4 | 84 h | 14,0 | **Vượt 4,0 ngày** ⇒ có thể tách "chốt điểm" sang Sprint 5 |
| Sprint 5 | 69 h | 11,5 | Vượt 1,5 ngày ⇒ giảm biểu đồ từ 4 xuống 3 |
| Sprint 6 | 105 h | 17,5 | **Vượt 7,5 ngày** ⇒ ưu tiên Excel/PDF/thông báo cơ bản; tài liệu làm song song từ Sprint 5 |
| **TỔNG** | **513,5 h** | **85,6** | ≈ 13 tuần × 10 ngày nếu làm 6–7 h/ngày |

**Kết luận quan trọng:** với **1 người làm 6 h/ngày**, các sprint 3–4–6 đều vượt trần 2 tuần. Ba cách xử lý (chọn 1):
1. **Tăng thời lượng:** mỗi sprint 3 tuần ⇒ tổng ~16 tuần (an toàn nhất cho đồ án).
2. **Cắt scope:** áp dụng ngay danh sách cắt ở cột "vượt" phía trên; giữ nguyên 2 tuần/sprint.
3. **Tăng ca:** 8 h/ngày ⇒ 10 ngày = 80 h (khớp với hầu hết sprint, trừ S4/S6).

### 10.1. Phương án 4 sprint × 1 tuần (nếu 1 mình & thời gian gấp)

| Sprint gộp | Nội dung gộp | Tương ứng sprint gốc |
|---|---|---|
| Tuần 1 | Setup + Auth + phân quyền + layout | S0 + S1 |
| Tuần 2 | Danh mục (Khoa/Ngành/Lớp/Môn/Học kỳ) + Sinh viên CRUD (không import) | S2 + một phần S3 |
| Tuần 3 | Import Excel + Lớp HP + Ghi danh + Nhập điểm | Phần còn lại S3 + S4 |
| Tuần 4 | Tra cứu điểm + Dashboard + PDF/Excel + tài liệu | S5 + S6 |

**Cắt bỏ trong phương án này:** thông báo (E9), tra cứu công khai, phân quyền chi tiết theo permission, tối ưu nâng cao. Ghi rõ trong phần "hướng phát triển" của báo cáo.

### 10.2. Thứ tự phụ thuộc (không được đảo)

```
S0 (nền tảng + enum + i18n)
  └─► S1 (auth + role + layout theo vai trò)
        ├─► S2 (Khoa → Ngành → Lớp; Môn; Học kỳ)      [bắt buộc trước S3]
        │     └─► S3 (Sinh viên — cần lop_id)          [bắt buộc trước S4]
        │           └─► S4 (Lớp HP → Ghi danh → Điểm)  [bắt buộc trước S5]
        │                 └─► S5 (Bảng điểm SV, GPA, Dashboard)
        │                       └─► S6 (Báo cáo PDF/Excel, Thông báo)
        └─► (song song) Admin quản lý tài khoản → tài khoản giảng viên cho S4
```
> Nếu cần "có gì đó để chấm sớm": hoàn thành S0 → S1 → S2 là đã có **một hệ thống CRUD + phân quyền chạy được** (đủ để demo giữa kỳ).

---

## 11. Rủi ro tổng thể & cách giảm thiểu

| Rủi ro | Mức | Giảm thiểu | Sprint liên quan |
|---|---|---|---|
| Phình phạm vi (thêm tính năng ngoài MVP) | Cao | Mọi yêu cầu mới ⇒ ghi vào `Backlog tương lai`, không chèn vào sprint đang chạy | Tất cả |
| Sai công thức/thang điểm so với quy chế | Cao | Chốt ADR-04 trước S4; để công thức trong `config/diem.php` + `DiemService` | S4, S5 |
| Package ngoài không tương thích Laravel 13 | Cao | Cài & test ở **đầu sprint**; luôn có phương án B (CSV, in từ trình duyệt) | S3, S6 |
| Lỗi import Excel do file người dùng | TB | Bắt buộc dùng file mẫu; validate từng dòng; báo lỗi theo dòng | S3 |
| Bảo mật (IDOR/XSS/mass assignment) | Cao | Form Request + Policy + Feature test cho mọi module | S1→S6 |
| Trễ tiến độ | Cao | Có quy tắc cắt scope (mục 2); mỗi sprint phải có demo chạy được | Tất cả |
| Mất dữ liệu khi phát triển | TB | `--seed` lại được; `docs/backup-db.bat` trước thao tác rủi ro | S3→S6 |
| WSL/Windows: symlink & quyền file | Thấp | Dùng Laragon, chạy `storage:link` ở terminal Admin | S0, S3 |
| Test SQLite khác MySQL | TB | Migration portable (ADR-07); chạy thêm `php artisan migrate:fresh` trên MySQL trước khi merge | Tất cả |
| Asset không build ⇒ UI vỡ | TB | `npm run build` trong DoD và checklist trước demo | Tất cả |

---

## 12. Roadmap sau MVP (Sprint 7+) — ghi vào phần "hướng phát triển"

| # | Tính năng | Mô tả ngắn | Ghi chú kỹ thuật |
|---|---|---|---|
| 7.1 | Học phí & thanh toán | Quản lý công nợ học phí theo tín chỉ, biên lai, xuất PDF | Bảng `tuitions`, `payments` |
| 7.2 | Điểm danh QR | GV tạo buổi học → SV quét QR điểm danh | Bảng `attendances`, thư viện QR JS |
| 7.3 | Đăng ký tín chỉ online | SV tự chọn lớp HP theo học kỳ, giới hạn tín chỉ | Thêm luồng duyệt + hàng đợi khi hết chỗ |
| 7.4 | API + Mobile app | REST API cho tra cứu điểm/thông báo | `php artisan install:api` + Sanctum |
| 7.5 | Thông báo realtime / email | Đẩy thông báo qua email, broadcast | Mail + queue + broadcasting |
| 7.6 | Báo cáo nâng cao | Thống kê nhiều năm, so sánh học kỳ, dự báo tốt nghiệp | Cache + export nâng cao |
| 7.7 | Nhật ký hoạt động (audit log) | Ghi ai sửa gì, khi nào | Package audit hoặc bảng `activity_logs` |
| 7.8 | Phân quyền chi tiết | Chuyển từ cột `role` sang Spatie Permission | Map `role` → Spatie roles, thêm permission theo chức năng |

---

## 13. Bảng lệnh thường dùng (cheat sheet)

```bat
:: ---- Chạy dự án ----
composer run dev              :: server + vite + queue + log (khuyến nghị)
php artisan serve             :: chỉ server, http://127.0.0.1:8000
npm run dev                   :: vite dev
npm run build                 :: build asset (BẮT BUỘC trước demo)

:: ---- Cơ sở dữ liệu ----
php artisan migrate                        :: chạy migration
php artisan migrate:fresh --seed           :: tạo lại DB + dữ liệu mẫu
php artisan migrate:status                 :: xem trạng thái
php artisan db:seed --class=DemoSeeder     :: seed riêng 1 seeder
php artisan db:show                        :: thông tin DB hiện tại

:: ---- Sinh code ----
php artisan make:model Student -mfs        :: model + migration + factory + seeder
php artisan make:controller StudentController --resource --model=Student
php artisan make:request StoreStudentRequest
php artisan make:enum Role
php artisan make:policy CoursePolicy --model=Course
php artisan make:middleware EnsureUserHasRole
php artisan make:test Students/StudentCrudTest --phpunit

:: ---- Kiểm tra chất lượng ----
php artisan test                                  :: chạy toàn bộ test
php artisan test --filter=DiemService             :: chạy 1 nhóm test
php artisan test --coverage                       :: độ phủ (cần Xdebug)
vendor/bin/pint --dirty                           :: format code đã sửa
vendor/bin/pint --test                            :: chỉ kiểm tra, không sửa
php artisan route:list                            :: rà soát route & middleware

:: ---- Triển khai / demo ----
php artisan optimize          :: cache config + route + view (chỉ khi APP_DEBUG=false)
php artisan optimize:clear    :: xoá cache khi sửa config/route
php artisan down / up         :: bật/tắt chế độ bảo trì
php artisan storage:link      :: link storage → public (làm 1 lần)
```

---

## 14. Checklist "bắt đầu ngay hôm nay" (Sprint 0 – Ngày 1)

- [ ] Mở **Laragon → Start All**; xác nhận PHP 8.3.30 + Composer 2.10.2 + MySQL 8.4 đang chạy.
- [ ] `npm install` → `npm run build` (đang **thiếu**, đây là việc gấp nhất của Sprint 0).
- [ ] Cập nhật `.env`: `APP_LOCALE=vi`, `APP_FALLBACK_LOCALE=vi`, `APP_FAKER_LOCALE=vi_VN`; đặt timezone `Asia/Ho_Chi_Minh`.
- [ ] `php artisan lang:publish` → tạo `lang/vi/validation.php` + `auth.php` + `passwords.php`.
- [ ] `php artisan storage:link` → tạo `storage/app/public/students`.
- [ ] Tạo branch `feature/s0-nen-tang`; commit `chore(setup): Sprint 0 nền tảng`.
- [ ] Tạo GitHub Project (cột To Do / In Progress / Review / Done) + import `DANH_SACH_TASK_SPRINT.csv`.
- [ ] Dựng `layouts/app.blade.php` + `partials/sidebar.blade.php` + `partials/flash.blade.php`; mở `/` xem layout.
- [ ] Viết `tests/Feature/HealthTest.php`; chạy `php artisan test` → xanh.
- [ ] **Chốt ADR-04 (trọng số 40/60 & thang điểm chữ) với giảng viên/giáo vụ** để không kẹt ở Sprint 4.
