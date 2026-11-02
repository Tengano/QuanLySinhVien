# 🎓 QuanLySinhVien — Hệ thống Quản lý Sinh viên

Website quản lý sinh viên thay thế việc quản lý thủ công bằng Excel: quản lý khoa/ngành/lớp,
hồ sơ sinh viên, lớp học phần, nhập điểm – tính GPA, thống kê và báo cáo.
Hệ thống có **4 vai trò**: **Admin**, **Giáo vụ**, **Giảng viên**, **Sinh viên**.

- Mã nguồn theo mô hình MVC của Laravel; nghiệp vụ viết bằng **tiếng Việt** (tên bảng, cột, model, controller, route, view).
- Cơ sở dữ liệu MySQL `qlsv`: **12 bảng nghiệp vụ đã Việt hoá** + 8 bảng hạ tầng mặc định của Laravel.
- Giao diện Blade + Tailwind CSS (không dùng SPA); báo cáo xuất **CSV** (mở bằng Excel) và in/Lưu **PDF** từ trình duyệt.

## 📚 Mục lục

1. [Công nghệ sử dụng](#-công-nghệ-sử-dụng)
2. [Kiến trúc & luồng xử lý](#-kiến-trúc--luồng-xử-lý)
3. [Vai trò & quyền](#-vai-trò--quyền)
4. [Chức năng theo phân hệ](#-chức-năng-theo-phân-hệ)
5. [Yêu cầu hệ thống](#-yêu-cầu-hệ-thống)
6. [Cài đặt](#️-cài-đặt)
7. [Tạo tài khoản đầu tiên](#-tạo-tài-khoản-đầu-tiên)
8. [Chạy dự án](#️-chạy-dự-án)
9. [Cấu trúc thư mục](#-cấu-trúc-thư-mục)
10. [Cơ sở dữ liệu](#️-cơ-sở-dữ-liệu)
11. [Quy tắc nghiệp vụ](#-quy-tắc-nghiệp-vụ)
12. [Quy ước code](#-quy-ước-code)
13. [Lộ trình phát triển](#️-lộ-trình-phát-triển)
14. [Tài liệu & ghi chú](#-tài-liệu--ghi-chú)

## 🧰 Công nghệ sử dụng

| Thành phần | Công nghệ |
|---|---|
| Backend | Laravel **13.35** (PHP **8.3**), Eloquent ORM, mô hình MVC |
| Cơ sở dữ liệu | MySQL 8.x — database `qlsv`, charset `utf8mb4` |
| Frontend | Blade Template + Tailwind CSS **4** + Vite **8** (`laravel-vite-plugin` 3, font Bunny “Instrument Sans”) |
| Xác thực | Auth session có sẵn của Laravel (model `NguoiDung`), rate limit đăng nhập 5 lần/phút |
| Phân quyền | Middleware alias `role` (`KiemTraVaiTro`) + `LopHocPhanPolicy` |
| Kiểm thử / code style | PHPUnit 12, Laravel Pint (dev dependency) |
| Quy trình | Git + Agile/Scrum (4 sprint) |

## 🏗 Kiến trúc & luồng xử lý

```text
Request
  → routes/web.php            (khai báo route, gắn middleware 'auth' + 'role:...')
  → KiemTraVaiTro             (chặn 403 theo vai trò)
  → Controller                (validate dữ liệu, authorize policy)
      ├── Service             (DiemService, GpaService, ThongKeService — nghiệp vụ thuần)
      └── Model               (Eloquent, quan hệ, enum cast, soft delete)
  → Blade view                (bo-cuc.chinh | bo-cuc.khach | bo-cuc.in)
```

- **Danh mục** dùng chung controller trừu tượng `DanhMucController`; mỗi danh mục chỉ cấu hình cột/nhãn/rule riêng → tránh lặp code.
- **Nghiệp vụ điểm** tập trung ở `DiemService` (TB – điểm chữ – hệ 4 – đạt) và `GpaService` (GPA, tín chỉ, xếp loại); tham số đọc từ `config/diem.php`.
- **Thống kê** tập trung ở `ThongKeService` (`admin()` / `giangVien()`), cache dashboard 5 phút.
- **Bố cục view**: `bo-cuc.chinh` (sidebar + topbar), `bo-cuc.khach` (đăng nhập), `bo-cuc.in` (bản in tối giản).
- Nếu chưa build asset (`public/build` và `public/hot` đều không có), layout tự nạp Tailwind qua CDN để vẫn hiển thị được.

## 👥 Vai trò & quyền

| Vai trò (`vai_tro`) | Việc chính | Menu hiển thị |
|---|---|---|
| **Admin** (`admin`) | Toàn quyền: tài khoản, danh mục, sinh viên, lớp HP, điểm, báo cáo, thông báo; xoá vĩnh viễn hồ sơ | Bảng điều khiển · Người dùng · Danh mục · Sinh viên · Lớp học phần · Thông báo |
| **Giáo vụ** (`giao_vu`) | Danh mục, hồ sơ sinh viên, lớp HP, ghi danh, nhập/mở khoá điểm, báo cáo, thông báo | Bảng điều khiển · Danh mục · Sinh viên · Lớp học phần · Thông báo |
| **Giảng viên** (`giang_vien`) | Xem lớp được phân công, nhập điểm và chốt điểm lớp mình dạy, xem bảng điểm lớp | Bảng điều khiển · Lớp học phần · Hộp thư |
| **Sinh viên** (`sinh_vien`) | Xem bảng điểm + GPA của mình, nhận thông báo | Bảng điều khiển · Bảng điểm của tôi · Hộp thư |

- Kiểm tra quyền ở 2 lớp: **middleware `role`** cho nhóm route và **policy** (`LopHocPhanPolicy::view/manage/manageEnrollments/manageGrades`) cho từng lớp học phần.
- Tài khoản bị khoá (`trang_thai = false`) không đăng nhập được; Admin không thể tự xoá/khoá tài khoản hay tự đổi vai trò của mình.

## ✨ Chức năng theo phân hệ

| Phân hệ | Chức năng | Route name chính |
|---|---|---|
| **Xác thực & hồ sơ** | Đăng nhập/đăng xuất (có “ghi nhớ đăng nhập”, chặn 5 lần/phút), hồ sơ cá nhân, đổi mật khẩu | `login`, `login.store`, `logout`, `profile.edit/update/password` |
| **Bảng điều khiển** | Dashboard theo vai trò: thẻ số liệu, SV theo khoa/ngành, tỉ lệ đạt–rớt theo môn, điểm TB theo kỳ (Admin/Giáo vụ); lớp phụ trách (Giảng viên); GPA cá nhân (Sinh viên) | `dashboard` |
| **Quản trị tài khoản** | Danh sách (tìm kiếm/lọc theo vai trò, trạng thái), tạo, sửa, xoá, khoá/mở khoá, reset mật khẩu về `Qlsv@123456` | `users.*` |
| **Danh mục** | Khoa, Ngành, Lớp hành chính, Môn học, Học kỳ: CRUD, tìm kiếm, chặn xoá khi còn dữ liệu con | `danh-muc.khoa.*`, `danh-muc.nganh.*`, `danh-muc.lop.*`, `danh-muc.mon-hoc.*`, `danh-muc.hoc-ky.*` |
| **Sinh viên** | CRUD hồ sơ, ảnh đại diện, tạo tài khoản đăng nhập kèm hồ sơ, tìm kiếm/lọc (khoa, ngành, lớp, giới tính, trạng thái), xoá mềm + thùng rác + khôi phục, import CSV, tải file mẫu, xuất CSV | `students.*` |
| **Lớp học phần** | Mở lớp từ Môn + Học kỳ + Giảng viên (mã lớp HP, phòng, lịch, sĩ số tối đa, trạng thái), sửa/xoá, xem chi tiết | `courses.index/show/create/store/edit/update/destroy` |
| **Ghi danh** | Gán sinh viên vào lớp HP (chặn trùng, chặn vượt sĩ số), rút sinh viên khi chưa chốt điểm | `courses.enroll.create/store`, `enrollments.destroy` |
| **Điểm** | Nhập điểm quá trình/thi → tự tính TB, điểm chữ, hệ 4, đạt; chốt điểm (khoá) và mở khoá | `courses.grades.edit/update/lock/unlock` |
| **Cổng sinh viên** | Sinh viên xem bảng điểm, GPA, xếp loại của chính mình | `portal.grades` |
| **Báo cáo** | Phiếu điểm sinh viên, bảng điểm lớp HP (bản in/Lưu PDF) | `reports.transcript`, `reports.grades` |
| **Thông báo** | Soạn thông báo theo toàn trường / khoa / lớp / lớp HP / một người dùng, xem danh sách, xoá | `announcements.index/create/store/destroy` |
| **Hộp thư** | Hộp thư cá nhân, đánh dấu đã đọc (từng thông báo hoặc tất cả) | `inbox.index/mark-all/show` |

## ✅ Yêu cầu hệ thống

- **PHP >= 8.3** (bật extension: `mbstring, openssl, pdo_mysql, tokenizer, xml, ctype, json, bcmath, fileinfo, gd, zip`)
- **Composer >= 2.x**
- **MySQL >= 8.0** (khuyến nghị 8.4) — tạo sẵn database `qlsv` với charset `utf8mb4`
- **Node.js >= 20.19** (hoặc >= 22.12) & npm — yêu cầu của Vite 8 để build asset
- **Git**

> Laragon/XAMPP đều dùng được: chỉ cần bật MySQL và trỏ thư mục web về `public/`.

## ⚙️ Cài đặt

```bash
# 1. Cài dependency PHP
composer install

# 2. Tạo file môi trường & khoá ứng dụng
copy .env.example .env          # macOS/Linux: cp .env.example .env
php artisan key:generate

# 3. Cấu hình MySQL trong .env (file mẫu mặc định là sqlite → phải sửa)
#    APP_NAME="QuanLySinhVien"
#    APP_URL=http://localhost:8000
#    DB_CONNECTION=mysql
#    DB_HOST=127.0.0.1
#    DB_PORT=3306
#    DB_DATABASE=qlsv
#    DB_USERNAME=root
#    DB_PASSWORD=

# 4. Tạo database 'qlsv' (utf8mb4) rồi chạy migration
php artisan migrate

# 5. Cài & build asset frontend
npm install
npm run build

# 6. Tạo symlink cho ảnh đại diện (lưu ở storage/app/public/students)
php artisan storage:link
```

Ghi chú:

- `composer run setup` có sẵn (install → tạo `.env` → key → migrate → npm install → build) nhưng **chưa** cấu hình MySQL và **chưa** tạo `storage:link`; chỉ nên dùng để khởi tạo nhanh rồi chỉnh `.env` lại.
- Dự án **chưa có `database/seeders` và `database/factories`**, nên sau khi migrate phải tạo tài khoản đầu tiên theo mục dưới.

## 🔑 Tạo tài khoản đầu tiên

Chạy `php artisan tinker` rồi dán đoạn sau (mật khẩu được tự băm nhờ cast `hashed`):

```php
App\Models\NguoiDung::create([
    'ho_ten'     => 'Quản trị viên',
    'email'      => 'admin@qlsv.test',
    'password'   => 'Qlsv@123456',
    'vai_tro'    => 'admin',
    'trang_thai' => true,
]);
```

Tương tự với `'vai_tro' => 'giao_vu' | 'giang_vien' | 'sinh_vien'`.

Tài khoản sinh viên nên tạo từ màn **Sinh viên → Thêm sinh viên** (tick “Tạo tài khoản đăng nhập”):
mật khẩu mặc định là `Qlsv@dmY` (ví dụ `Qlsv@02112026`), Admin có thể reset về `Qlsv@123456`.

## ▶️ Chạy dự án

```bash
php artisan serve          # http://127.0.0.1:8000
npm run dev                # Vite dev server (hot reload) — chạy song song ở terminal khác
```

Với Laragon có thể mở trực tiếp `http://quanlysinhvien.test` (virtual host trỏ vào `public/`).

## 📂 Cấu trúc thư mục

```text
app/
├── Enums/                     VaiTro, GioiTinh, TrangThaiSinhVien, TrangThaiLopHocPhan,
│                              TrangThaiDangKy, LoaiMonHoc, DoiTuongThongBao (+ Concerns/HasLabel)
├── Http/
│   ├── Controllers/
│   │   ├── Auth/DangNhapController.php
│   │   ├── BangDieuKhienController.php      dashboard theo vai trò
│   │   ├── DanhMucController.php            controller trừu tượng dùng chung cho 5 danh mục
│   │   ├── KhoaController.php   NganhController.php   LopController.php
│   │   ├── MonHocController.php   HocKyController.php
│   │   ├── SinhVienController.php           CRUD, import/export CSV, thùng rác
│   │   ├── LopHocPhanController.php   GhiDanhController.php   DiemController.php
│   │   ├── NguoiDungController.php   HoSoCaNhanController.php
│   │   ├── ThongBaoController.php   HopThuController.php
│   │   └── CongSinhVienController.php   BaoCaoController.php
│   └── Middleware/KiemTraVaiTro.php         alias 'role'
├── Models/                    NguoiDung, Khoa, Nganh, Lop, SinhVien, MonHoc, HocKy,
│                              LopHocPhan, GhiDanh, Diem, ThongBao
├── Policies/LopHocPhanPolicy.php
├── Providers/AppServiceProvider.php         rate limiter 'login'
└── Services/                  DiemService, GpaService, ThongKeService

bootstrap/app.php              đăng ký alias middleware 'role', redirect guest/user
config/                        app, auth, diem (trọng số & bảng quy đổi điểm), cache, database, ...
database/migrations/           3 migration hạ tầng + 12 migration nghiệp vụ + 1 migration Việt hoá
                               2026_11_01_000000_doi_ten_bang_va_cot_sang_tieng_viet.php (đổi tên bảng/cột sang tiếng Việt)
public/                        index.php, build/ (asset đã build), storage/ (symlink)
resources/
├── css/app.css                Tailwind 4 (@import 'tailwindcss')
├── js/app.js
└── views/
    ├── bo-cuc/                chinh, khach, in
    ├── thanh-phan/            thanh-ben, thanh-tren, thong-bao
    ├── xac-thuc/              dang-nhap
    ├── bang-dieu-khien/       tong-quan + partial _quan-tri, _quan-tri-bang, _giang-vien, _sinh-vien
    ├── quan-tri/              nguoi-dung/, danh-muc/
    ├── sinh-vien/             danh-sach, bieu-mau, chi-tiet, nhap-du-lieu, thung-rac
    ├── lop-hoc-phan/          danh-sach, bieu-mau, chi-tiet, diem, ghi-danh
    ├── thong-bao/             danh-sach, bieu-mau
    ├── hop-thu/               danh-sach, chi-tiet
    ├── cong-sinh-vien/        bang-diem
    ├── bao-cao/               phieu-diem, bang-diem-lop
    ├── ho-so/                 chinh-sua
    └── trang-chao.blade.php
routes/web.php                 toàn bộ route (không tách api)
vite.config.js                 Tailwind 4 + font Bunny
PHAN_CHIA_SPRINT.md            kế hoạch sprint
TONG_HOP_QLSV.md               tổng hợp nghiệp vụ
```

## 🗄️ Cơ sở dữ liệu

| Nhóm | Bảng | Ghi chú |
|---|---|---|
| Tài khoản | `nguoi_dung` | `vai_tro`, `trang_thai`, `anh_dai_dien`, `dang_nhap_cuoi_luc` |
| Danh mục | `khoa`, `nganh`, `lop`, `mon_hoc`, `hoc_ky` | `lop` có `giao_vien_chu_nhiem_id`; `mon_hoc` có `so_tin_chi`, `loai_mh`, số tiết LT/TH |
| Sinh viên | `sinh_vien` | Xoá mềm, `ma_sv` duy nhất, ảnh, `trang_thai` |
| Lớp HP | `lop_hoc_phan` (`ma_lhp`, `so_sv_toi_da`, `phong_hoc`, `lich_hoc`), `ghi_danh` | Ghi danh nối `sinh_vien` ↔ `lop_hoc_phan` |
| Điểm | `diem` | 1 ghi danh ↔ 1 bộ điểm; có `khoa_luc` để chốt |
| Thông báo | `thong_bao`, `thong_bao_nguoi_dung` | Bảng pivot lưu `da_doc`, `doc_luc` |

- **Quan hệ:** `Khoa → Ngành → Lớp → Sinh viên`; `Môn học + Học kỳ + Giảng viên → Lớp HP → Ghi danh → Điểm`.
- **Khoá ngoại:** `restrict` khi cha còn dữ liệu con (khoa/ngành/lớp/môn/học kỳ), `cascade` cho `ghi_danh`, `diem`, `thong_bao_nguoi_dung`, `null` cho giảng viên/người nhập.
- 8 bảng hạ tầng giữ nguyên tên gốc: `migrations`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `sessions`, `password_reset_tokens`.
- Cột Laravel bắt buộc vẫn giữ tên gốc: `id`, `email`, `password`, `remember_token`, `email_verified_at`, `created_at`, `updated_at`, `deleted_at`.

## 📐 Quy tắc nghiệp vụ

Tham số nằm trong `config/diem.php` (trọng số `0.4 / 0.6`, `diem_dat = 4.0`, bảng `quy_doi`).

- **Điểm TB** = quá trình × 0,4 + thi × 0,6, làm tròn 2 chữ số; **đạt** khi TB ≥ 4,0.
- **Quy đổi:** ≥ 8,5 → A (4,0) · ≥ 8,0 → B+ (3,5) · ≥ 7,0 → B (3,0) · ≥ 6,5 → C+ (2,5) · ≥ 5,5 → C (2,0) · ≥ 5,0 → D+ (1,5) · ≥ 4,0 → D (1,0) · dưới 4,0 → F (0,0).
- **GPA** = Σ(điểm hệ 4 × số tín chỉ) / Σ số tín chỉ; **xếp loại**: Xuất sắc ≥ 3,60 · Giỏi ≥ 3,20 · Khá ≥ 2,50 · Trung bình ≥ 2,00 · Yếu < 2,00. Môn học lại chỉ lấy lần có điểm hệ 4 cao nhất.
- **Chốt điểm:** lớp đã chốt thì không sửa được; chỉ Admin/Giáo vụ mới mở khoá.
- **Ghi danh:** không trùng sinh viên trong cùng lớp HP, không vượt `so_sv_toi_da`; không rút được sinh viên đã chốt điểm.
- **Xoá dữ liệu:** không xoá Khoa/Ngành/Lớp/Môn/Học kỳ khi còn dữ liệu con; xoá sinh viên là **xoá mềm** (thùng rác + khôi phục), chỉ Admin xoá vĩnh viễn.
- **Xem dữ liệu:** sinh viên chỉ xem điểm của mình; giảng viên chỉ xem/nhập điểm lớp mình dạy; phiếu điểm trái quyền trả về 403.
- **Import CSV:** bắt buộc có cột `ma_sv, ho_ten, ngay_sinh, gioi_tinh, ma_lop` (tuỳ chọn `email, sdt, cccd, dia_chi`); file mẫu tải từ route `students.template`; dòng lỗi được liệt kê lại, dòng hợp lệ vẫn được nhập.

## 🧭 Quy ước code

- **Đặt tên tiếng Việt** cho domain: model/route/view/bảng/cột (`sinh_vien`, `lop_hoc_phan`, `diem`, `ghi_danh`...). Tên route dùng tiếng Anh ngắn (`students.*`, `courses.*`, `announcements.*`).
- **Enum PHP 8.1** cho các tập giá trị cố định, dùng trait `App\Enums\Concerns\HasLabel` để có `label()` và `options()` cho select box.
- **Model**: khai báo `$table`, `casts()`, quan hệ; dùng attribute `#[Fillable([...])]` của Laravel 13; `SoftDeletes` cho `SinhVien`.
- **Controller**: validate và thông báo lỗi tiếng Việt ngay trong controller (`messages()`), authorize bằng policy, đẩy nghiệp vụ sang `app/Services` (`final class`).
- **View**: mỗi màn hình một file theo phân hệ; form dùng chung `bieu-mau.blade.php` cho create/edit; layout `bo-cuc.chinh | khach | in`; flash message render qua `thanh-phan.thong-bao`.
- **Ngôn ngữ**: `APP_LOCALE` vẫn là `en` (không có thư mục `lang/`), nhãn tiếng Việt viết trực tiếp trong view và message validation tuỳ biến.
- Định dạng code: `vendor/bin/pint`.

## 🗺️ Lộ trình phát triển

| Sprint | Thời gian | Nội dung | Kết quả |
|:--|:--|:--|:--|
| **SP1** | Tuần 1–2 | Đăng nhập, phân quyền 4 vai trò, quản lý tài khoản, hồ sơ cá nhân | 4 vai trò vào đúng màn hình; sai quyền bị chặn 403 | ✅ |
| **SP2** | Tuần 3–4 | Khoa/Ngành/Lớp, Môn học/Học kỳ, Sinh viên (CRUD + import CSV), Lớp HP, ghi danh | Tạo đủ dữ liệu nền, mở lớp và ghi danh được | ✅ |
| **SP3** | Tuần 5–6 | Nhập điểm, tự tính TB/điểm chữ/hệ 4, chốt – mở khoá, cổng sinh viên | Nhập 8/9 → TB 8,60 (A); chốt xong không sửa được | ✅ |
| **SP4** | Tuần 7–8 | Dashboard + biểu đồ, xuất CSV, in bảng điểm, hộp thư/thông báo | Dashboard có số liệu, xuất được Excel và in bảng điểm | ✅ |

Chi tiết hạng mục và thứ tự thực hiện: [`PHAN_CHIA_SPRINT.md`](PHAN_CHIA_SPRINT.md).

## 📎 Tài liệu & ghi chú

- [`TONG_HOP_QLSV.md`](TONG_HOP_QLSV.md) — tổng hợp phạm vi, 5 nhóm chức năng, dữ liệu và quy tắc nghiệp vụ.
- [`PHAN_CHIA_SPRINT.md`](PHAN_CHIA_SPRINT.md) — kế hoạch 4 sprint và thứ tự công việc.
- **Hạn chế hiện tại:**
  - Chưa có `database/seeders`, `database/factories` → phải tạo tài khoản đầu tiên thủ công (xem [Tạo tài khoản đầu tiên](#-tạo-tài-khoản-đầu-tiên)).
  - Chưa có test tự động (`tests/` và `phpunit.xml` chưa tồn tại) dù đã cài PHPUnit.
  - Báo cáo chỉ xuất **CSV** (không xuất XLSX); “PDF” là chức năng In/Lưu PDF của trình duyệt.
  - Biểu đồ dashboard dựng bằng HTML/CSS từ số liệu của `ThongKeService`, không dùng thư viện chart JS.

