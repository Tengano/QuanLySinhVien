# User Story · Product Backlog · Kế hoạch Sprint — Website Quản lý Sinh viên

> **Tài liệu tham chiếu:** `Vi-du-sprint.pdf` — ví dụ về chuỗi *User Story → Product Backlog → Sprint Backlog → Lộ trình phát hành* cho một hệ thống thi trắc nghiệm.
> Tài liệu này **giữ nguyên cấu trúc** của ví dụ đó nhưng **áp dụng cho nghiệp vụ Quản lý Sinh viên** của repo (không sao chép các yêu cầu của hệ thống thi).
>
> | Hạng mục | Giá trị |
> |---|---|
> | Stack | Laravel 13.35 · PHP 8.3 · MySQL 8.4 · Tailwind CSS 4 + Vite 8 · PHPUnit 12 |
> | Mô hình | Scrum rút gọn: **Sprint 0 (1 tuần) + Sprint 1–6 (2 tuần)** ≈ 13 tuần |
> | Quy mô backlog | **29 User Story · 137 Story Point** + 9 task nền tảng Sprint 0 |
> | Tài liệu liên quan | `AGILE_QLSV.md`, `SPRINT_CHI_TIET.md`, `DANH_SACH_TASK_SPRINT.csv` |
> | Cập nhật | 18/10/2026 |
>
> **Đọc nhanh:** Mục 2 = User Story + Acceptance Criteria · Mục 3 = Product Backlog · Mục 4–5 = kế hoạch & Sprint Backlog · Mục 6 = lộ trình phát hành · Mục 7 = backlog giai đoạn sau.

---

## 1. Quy ước dùng trong tài liệu

### 1.1. Tác nhân (Actor)

| Mã | Tác nhân | Quyền hạn chính |
|---|---|---|
| Khách | Người chưa đăng nhập | Chỉ xem trang đăng nhập; mọi route nghiệp vụ chuyển về `/login` |
| Admin | Quản trị viên | Toàn quyền: tài khoản, danh mục, sinh viên, lớp HP, điểm, báo cáo, thông báo |
| Giáo vụ | Nhân viên giáo vụ | Danh mục, hồ sơ sinh viên, lớp HP, ghi danh, hỗ trợ nhập điểm, báo cáo, thông báo |
| Giảng viên | Giảng viên | Xem lớp được phân công, nhập/chốt điểm, dashboard giảng viên |
| Sinh viên | Sinh viên | Xem bảng điểm/GPA của mình, nhận thông báo, dashboard sinh viên |

### 1.2. Epic → Sprint

| Epic | Nội dung | User Story | Sprint |
|---|---|---|---|
| **E1** | Xác thực & phân quyền | US-01 → US-04 | Sprint 1 |
| **E2** | Danh mục: Khoa · Ngành · Lớp hành chính | US-05 → US-07 | Sprint 2 |
| **E4** | Môn học & Học kỳ | US-08 → US-10 | Sprint 2 |
| **E3** | Quản lý Sinh viên | US-11 → US-15 | Sprint 3 |
| **E5** | Lớp học phần & ghi danh | US-16, US-17 | Sprint 4 |
| **E6** | Nhập điểm, tính điểm, tra cứu điểm/GPA | US-18 → US-22 | Sprint 4–5 |
| **E7** | Dashboard & thống kê | US-23 → US-25 | Sprint 5 |
| **E8** | Báo cáo Excel/PDF | US-26, US-27 | Sprint 6 |
| **E9** | Thông báo nội bộ | US-28, US-29 | Sprint 6 |
| **–** | Nền tảng kỹ thuật (enabler) | EN-01 → EN-09 | Sprint 0 |

### 1.3. Ưu tiên & Story Point

| Mức | Ý nghĩa | Cách xử lý khi trễ |
|---|---|---|
| **P1 — Rất cao** | Bắt buộc cho MVP; thiếu thì không demo được | Không cắt, chỉ cắt việc khác |
| **P2 — Cao** | Thuộc MVP | Cố giữ, có thể giản lược AC |
| **P3 — Trung bình** | "Nên có", tăng trải nghiệm | Cắt trước, chuyển sprint sau |
| **P4 — Thấp** | Ngoài MVP, giai đoạn sau | Không đưa vào sprint hiện tại |

- **Story Point (SP)** dùng dãy Fibonacci `1 · 2 · 3 · 5 · 8 · 13`, đo **độ phức tạp tương đối**; **không quy đổi trực tiếp ra giờ**.
- Năng lực tham chiếu của kế hoạch: **17–27 SP/sprint** cho nhóm 1–2 người; số giờ chi tiết (68–105 h/sprint) lấy từ `DANH_SACH_TASK_SPRINT.csv`, đã gồm test + tài liệu + buffer.
- Vì vậy một sprint có thể ít SP nhưng nhiều giờ nếu phần lớn là việc lặp lại (CRUD danh mục) hoặc nhiều việc không thuộc User Story (UAT, tài liệu, hardening).

### 1.4. Definition of Ready (DoR) & Definition of Done (DoD)

**DoR — Story chỉ được đưa vào Sprint khi:**
- Có mã US, mô tả "Là một…, tôi muốn…, để…" và AC rõ ràng.
- Biết file/route/màn hình cần tạo và task kỹ thuật tương ứng trong `DANH_SACH_TASK_SPRINT.csv`.
- Không vướng phụ thuộc chưa xong; task con ước lượng ≤ 8 giờ.

**DoD — Story chỉ được coi là Done khi:**
- Tất cả AC pass; có Feature/Unit test và `php artisan test` xanh.
- `php artisan migrate:fresh --seed` chạy sạch trên MySQL.
- Có validate (Form Request), chống SQL Injection/XSS/CSRF, chặn IDOR.
- Giao diện responsive, tiếng Việt, không lỗi console; `vendor/bin/pint --test` pass.
- Task được kéo sang cột **Done** trên bảng sprint và cập nhật tài liệu sprint.

---

## 2. User Story & Acceptance Criteria

### 2.1. Khách & người dùng đã đăng nhập

#### US-01 — Đăng nhập / đăng xuất
- **Tác nhân:** Khách, mọi vai trò
- **User Story:** Là một người dùng, tôi muốn đăng nhập bằng email và mật khẩu để truy cập đúng chức năng theo vai trò của mình, và đăng xuất an toàn khi dùng xong.
- **Acceptance Criteria:**
  - Form đăng nhập có email + mật khẩu, hiển thị lỗi tiếng Việt; mật khẩu không hiển thị dạng thô.
  - Đăng nhập đúng → chuyển `/dashboard` tương ứng vai trò (`admin` / `giao_vu` / `giang_vien` / `sinh_vien`).
  - Sai 5 lần/phút → chặn tạm thời (HTTP 429) với thông báo tiếng Việt.
  - Tài khoản có `trang_thai = false` không đăng nhập được, thông báo "Tài khoản đã bị khoá".
  - Đăng xuất huỷ session và quay về `/login`; truy cập route `auth` khi chưa đăng nhập bị chuyển về login.
  - Cập nhật `last_login_at` sau mỗi lần đăng nhập thành công.
- **Ưu tiên / SP / Sprint:** P1 · 5 · Sprint 1

#### US-04 — Hồ sơ cá nhân & đổi mật khẩu
- **Tác nhân:** Mọi người dùng đã đăng nhập
- **User Story:** Là một người dùng, tôi muốn xem/sửa hồ sơ và đổi mật khẩu của mình để thông tin cá nhân luôn đúng và tài khoản được bảo vệ.
- **Acceptance Criteria:**
  - Sửa được `name`, `email`, `so_dien_thoai`, `avatar`; **không** sửa được `role`, `trang_thai`; email mới không được trùng tài khoản khác.
  - Đổi mật khẩu yêu cầu mật khẩu hiện tại + xác nhận; mật khẩu mới tối thiểu 8 ký tự.
  - Ảnh đại diện chỉ nhận `jpg/jpeg/png` ≤ 2 MB, lưu trên disk `public`; cập nhật/xoá ảnh phải xoá file cũ.
  - Topbar hiển thị tên và avatar mới sau khi lưu; thông báo thành công bằng tiếng Việt.
- **Ưu tiên / SP / Sprint:** P2 · 3 · Sprint 1

### 2.2. Quản trị viên

#### US-02 — Kiểm soát truy cập theo vai trò
- **Tác nhân:** Admin (cấu hình), tất cả vai trò (áp dụng)
- **User Story:** Là một quản trị viên, tôi muốn hệ thống chặn mọi truy cập trái quyền theo vai trò để dữ liệu học vụ và tài khoản luôn được bảo vệ.
- **Acceptance Criteria:**
  - Middleware `role:...` chặn truy cập sai quyền bằng **HTTP 403** kèm trang lỗi tiếng Việt, không lộ stack trace.
  - Sidebar/topbar chỉ hiển thị mục người dùng có quyền; gõ URL trực tiếp vẫn bị chặn.
  - Sinh viên **không** vào được `/users`, `/danh-muc`, `/sinh-vien`, `/lop-hoc-phan`; giảng viên chỉ thấy lớp HP được phân công.
  - Mọi route nghiệp vụ nằm trong nhóm `['auth', 'role:...']`; `php artisan route:list` hiển thị đúng middleware.
  - Có Feature Test cho 4 vai trò: được phép (200) và bị chặn (403).
- **Ưu tiên / SP / Sprint:** P1 · 3 · Sprint 1

#### US-03 — Quản lý tài khoản người dùng
- **Tác nhân:** Admin
- **User Story:** Là một quản trị viên, tôi muốn tạo, sửa, khoá/mở và cấp lại mật khẩu tài khoản để kiểm soát người dùng hệ thống.
- **Acceptance Criteria:**
  - Danh sách tài khoản có tìm kiếm (tên/email), lọc theo vai trò + trạng thái, phân trang và sắp xếp.
  - Tạo/sửa tài khoản với `name`, `email` (unique), `role` (1 trong 4 vai trò), `so_dien_thoai`.
  - Reset mật khẩu về mật khẩu tạm và chỉ hiển thị một lần; khoá/mở bằng `trang_thai`.
  - `UserPolicy` chặn admin tự xoá, tự khoá và tự hạ quyền của chính mình.
  - Không xoá được tài khoản đã liên kết hồ sơ sinh viên; hiển thị thông báo thân thiện.
  - Có test cho CRUD, reset, khoá tài khoản và các ràng buộc policy.
- **Ưu tiên / SP / Sprint:** P1 · 8 · Sprint 1

#### US-23 — Dashboard tổng quan cho Admin/Giáo vụ
- **Tác nhân:** Admin, Giáo vụ
- **User Story:** Là một quản trị viên/giáo vụ, tôi muốn thấy các chỉ số và biểu đồ tổng quan của trường để nắm nhanh tình hình đào tạo thay vì đếm tay trên Excel.
- **Acceptance Criteria:**
  - 4 card: tổng sinh viên đang học, tổng lớp HP đang mở, tổng môn học, tổng giảng viên.
  - 4 biểu đồ: số SV theo Khoa; tỉ lệ SV theo Ngành (top 6); số SV Đạt/Không đạt theo môn (top 8); điểm TB toàn trường theo học kỳ.
  - Số liệu khớp truy vấn thủ công; có cache 5 phút và nút "Làm mới số liệu".
  - Dashboard tải < 1,5 giây với dữ liệu seed; biểu đồ render đúng khi đổi kích thước màn hình.
  - Chỉ Admin/Giáo vụ truy cập; không lộ dữ liệu điểm chi tiết của sinh viên.
- **Ưu tiên / SP / Sprint:** P2 · 8 · Sprint 5

### 2.3. Giáo vụ

**Nhóm Danh mục (Epic E2 + E4)**

#### US-05 — Quản lý Khoa
- **Tác nhân:** Giáo vụ, Admin
- **User Story:** Là một giáo vụ, tôi muốn thêm/sửa/xoá/tìm kiếm Khoa để chuẩn hoá dữ liệu gốc trước khi nhập ngành, lớp và sinh viên.
- **Acceptance Criteria:**
  - CRUD Khoa gồm `ma_khoa` (unique), `ten_khoa`, `truong_khoa`, `dien_thoai`, `email`, `dia_chi`.
  - Danh sách có tìm kiếm theo mã/tên, sắp xếp và phân trang 10–15 dòng.
  - Validate bắt buộc mã + tên, mã không trùng; thông báo lỗi tiếng Việt.
  - Chặn xoá Khoa khi còn Ngành hoặc Môn học; hiển thị lý do thay vì lỗi 500.
  - Chỉ Admin/Giáo vụ truy cập; giảng viên và sinh viên nhận 403.
- **Ưu tiên / SP / Sprint:** P1 · 3 · Sprint 2

#### US-06 — Quản lý Ngành
- **Tác nhân:** Giáo vụ, Admin
- **User Story:** Là một giáo vụ, tôi muốn quản lý Ngành thuộc từng Khoa để phân loại chương trình đào tạo và làm cơ sở tạo lớp hành chính.
- **Acceptance Criteria:**
  - CRUD Ngành gồm `faculty_id` (bắt buộc), `ma_nganh` (unique), `ten_nganh`, `so_nam_dao_tao` (1–6, mặc định 4).
  - Form chọn Khoa dạng combobox có tìm kiếm; danh sách hiển thị tên Khoa.
  - Lọc được theo Khoa; tìm theo mã/tên ngành.
  - Chặn xoá Ngành khi còn Lớp hành chính; thông báo tiếng Việt.
- **Ưu tiên / SP / Sprint:** P1 · 3 · Sprint 2

#### US-07 — Quản lý Lớp hành chính
- **Tác nhân:** Giáo vụ, Admin
- **User Story:** Là một giáo vụ, tôi muốn quản lý lớp hành chính (niên khoá, cố vấn học tập) để gán sinh viên vào đúng lớp.
- **Acceptance Criteria:**
  - CRUD Lớp gồm `major_id`, `ma_lop` (unique), `ten_lop`, `khoa_hoc`, `gvcn_id`.
  - `gvcn_id` chỉ chọn được user có vai trò `giang_vien`; được để trống.
  - Lọc theo Khoa/Ngành/niên khoá; tìm theo mã/tên lớp.
  - Chặn xoá Lớp khi còn Sinh viên; hiển thị số lượng SV đang thuộc lớp.
- **Ưu tiên / SP / Sprint:** P1 · 3 · Sprint 2

#### US-08 — Quản lý Môn học
- **Tác nhân:** Giáo vụ, Admin
- **User Story:** Là một giáo vụ, tôi muốn quản lý môn học kèm số tín chỉ và loại môn để phục vụ mở lớp học phần và tính GPA.
- **Acceptance Criteria:**
  - CRUD Môn học gồm `ma_mh` (unique), `ten_mh`, `so_tin_chi` (1–6), `loai_mh` (`bat_buoc`/`tu_chon`), số tiết lý thuyết/thực hành, Khoa (có thể trống).
  - Lọc theo Khoa, loại môn; tìm theo mã/tên môn.
  - Chặn xoá Môn khi đã có Lớp học phần; thông báo rõ.
- **Ưu tiên / SP / Sprint:** P2 · 3 · Sprint 2

#### US-09 — Quản lý Học kỳ
- **Tác nhân:** Giáo vụ, Admin
- **User Story:** Là một giáo vụ, tôi muốn tạo học kỳ/năm học kèm thời gian bắt đầu–kết thúc để mở lớp HP và tổng hợp điểm theo kỳ.
- **Acceptance Criteria:**
  - CRUD Học kỳ gồm `ten_hoc_ky` (HK1/HK2/HK Hè), `hoc_ky_so` (1–3), `nam_hoc` dạng `YYYY-YYYY`, `ngay_bat_dau`, `ngay_ket_thuc`.
  - Ràng buộc unique (`nam_hoc`, `hoc_ky_so`); `ngay_ket_thuc` phải sau `ngay_bat_dau`.
  - Danh sách sắp xếp năm học/kỳ mới nhất trước; hiển thị trạng thái đang diễn ra.
  - Chặn xoá Học kỳ khi đã có Lớp học phần.
- **Ưu tiên / SP / Sprint:** P2 · 2 · Sprint 2

#### US-10 — Tra cứu & bảo vệ ràng buộc dữ liệu danh mục
- **Tác nhân:** Giáo vụ, Admin
- **User Story:** Là một giáo vụ, tôi muốn tìm kiếm/lọc nhanh 5 danh mục và được cảnh báo khi xoá dữ liệu đang được dùng để tránh làm hỏng dữ liệu học vụ.
- **Acceptance Criteria:**
  - Cả 5 màn index đều có ô tìm kiếm, bộ lọc phù hợp, phân trang và giữ tham số khi chuyển trang.
  - Xoá dữ liệu đang được tham chiếu bị chặn ở tầng DB (`restrictOnDelete`) **và** hiển thị thông báo thân thiện ở UI.
  - Thông báo validate/unique hiển thị tiếng Việt, không lộ chi tiết SQL.
  - Dữ liệu seed mẫu đủ để CRUD và test: ≥ 3 Khoa, 6 Ngành, 6 Lớp, 8 Môn, 3 Học kỳ.
  - Feature Test 5 module + test phân quyền Admin/Giáo vụ (200) và Giảng viên/Sinh viên (403).
- **Ưu tiên / SP / Sprint:** P3 · 3 · Sprint 2

**Nhóm Quản lý Sinh viên (Epic E3)**

#### US-11 — Quản lý hồ sơ sinh viên
- **Tác nhân:** Giáo vụ, Admin
- **User Story:** Là một giáo vụ, tôi muốn thêm/sửa/xem/xoá hồ sơ sinh viên kèm ảnh đại diện để thay thế hoàn toàn sổ và file Excel thủ công.
- **Acceptance Criteria:**
  - CRUD hồ sơ gồm `ma_sv` (unique, kể cả với bản ghi đã xoá mềm), `ho_ten`, `ngay_sinh`, `gioi_tinh`, `lop_id`, `email`, `sdt`, `cccd` (unique), `dia_chi`, `trang_thai`, `ngay_nhap_hoc`, `ghi_chu`.
  - Validate: mã SV không trùng, ngày sinh trước hôm nay, lớp tồn tại, email/số điện thoại đúng định dạng, ảnh `jpg/jpeg/png` ≤ 2 MB.
  - Ảnh lưu `students/{ma_sv}-{timestamp}.{ext}` trên disk `public`; cập nhật/xoá phải xoá file cũ.
  - Trang chi tiết hiển thị đủ hồ sơ, lớp–ngành–khoa và ảnh; danh sách sắp xếp theo mã SV.
  - Chỉ Admin/Giáo vụ tạo/sửa/xoá; giảng viên chỉ xem hồ sơ SV thuộc lớp mình dạy.
- **Ưu tiên / SP / Sprint:** P1 · 8 · Sprint 3

#### US-12 — Nhập sinh viên hàng loạt từ Excel
- **Tác nhân:** Giáo vụ, Admin
- **User Story:** Là một giáo vụ, tôi muốn tải lên file Excel theo mẫu để nhập hàng loạt sinh viên, tiết kiệm thời gian và giảm sai sót so với nhập tay.
- **Acceptance Criteria:**
  - Có nút tải **file mẫu `.xlsx`** với đúng thứ tự cột: `ma_sv`, `ho_ten`, `ngay_sinh`, `gioi_tinh`, `ma_lop`, `email`, `sdt`, `cccd`, `dia_chi`.
  - Chỉ nhận `.xlsx` ≤ 5 MB; file sai định dạng bị từ chối với thông báo tiếng Việt.
  - Validate từng dòng; dòng lỗi được báo rõ **số dòng + cột + lý do**, các dòng hợp lệ vẫn được import.
  - Chống trùng `ma_sv` trong file và trong DB; import trong transaction, không tạo bản ghi dở dang.
  - Hiển thị kết quả: "Thành công X dòng – Lỗi Y dòng" kèm bảng lỗi tải về được.
- **Ưu tiên / SP / Sprint:** P1 · 8 · Sprint 3

#### US-13 — Tìm kiếm, lọc & phân trang sinh viên
- **Tác nhân:** Giáo vụ, Admin
- **User Story:** Là một giáo vụ, tôi muốn tìm và lọc sinh viên theo nhiều tiêu chí để tra cứu nhanh một hồ sơ trong hàng nghìn bản ghi.
- **Acceptance Criteria:**
  - Tìm theo mã SV, họ tên, email, số điện thoại; không phân biệt hoa/thường và có dấu/không dấu ở mức hợp lý.
  - Lọc theo Khoa → Ngành → Lớp, trạng thái học, giới tính; kết hợp được nhiều tiêu chí.
  - Giữ nguyên bộ lọc khi chuyển trang/sắp xếp; có nút "Xoá bộ lọc".
  - Danh sách eager-load quan hệ, không N+1; phản hồi < 1,5 giây với 1.000 SV seed.
- **Ưu tiên / SP / Sprint:** P2 · 5 · Sprint 3

#### US-14 — Xoá mềm & khôi phục sinh viên
- **Tác nhân:** Giáo vụ, Admin
- **User Story:** Là một giáo vụ, tôi muốn xoá mềm và khôi phục hồ sơ sinh viên để tránh mất dữ liệu do thao tác nhầm.
- **Acceptance Criteria:**
  - Xoá mềm: bản ghi biến khỏi danh sách chính nhưng còn trong "Thùng rác".
  - Khôi phục được hồ sơ; hiển thị lại đúng lớp và trạng thái trước đó.
  - Xoá vĩnh viễn chỉ Admin thực hiện, có hộp thoại xác nhận và xoá kèm ảnh.
  - `ma_sv` của bản ghi đã xoá mềm **vẫn bị chặn trùng** để bảo toàn lịch sử điểm.
- **Ưu tiên / SP / Sprint:** P3 · 3 · Sprint 3

#### US-15 — Cấp tài khoản đăng nhập cho sinh viên
- **Tác nhân:** Giáo vụ, Admin
- **User Story:** Là một giáo vụ, tôi muốn tạo tài khoản đăng nhập ngay khi thêm sinh viên để sinh viên có thể tra cứu điểm của mình.
- **Acceptance Criteria:**
  - Checkbox "Tạo tài khoản đăng nhập"; khi chọn thì **bắt buộc** có email.
  - Tạo user vai trò `sinh_vien`, mật khẩu mặc định `Qlsv@{ddmmyyyy}`, gán `students.user_id`.
  - Mật khẩu tạm chỉ hiển thị một lần sau khi lưu; không ghi log mật khẩu.
  - Email đã tồn tại → báo lỗi, không tạo tài khoản trùng; hồ sơ SV vẫn được lưu nếu người dùng bỏ chọn tạo tài khoản.
  - Sinh viên đăng nhập chỉ thấy điểm/thông báo của chính mình (test IDOR).
- **Ưu tiên / SP / Sprint:** P2 · 3 · Sprint 3

**Nhóm Lớp học phần & ghi danh (Epic E5)**

#### US-16 — Mở lớp học phần & phân công giảng viên
- **Tác nhân:** Giáo vụ, Admin
- **User Story:** Là một giáo vụ, tôi muốn mở lớp học phần từ môn + học kỳ và phân công giảng viên để bắt đầu tổ chức giảng dạy và nhập điểm.
- **Acceptance Criteria:**
  - CRUD lớp HP gồm `ma_lhp` (unique), `subject_id`, `semester_id`, `giang_vien_id`, `phong_hoc`, `lich_hoc`, `so_sv_toi_da`, `trang_thai` (`mo`/`dong`/`ket_thuc`), `ghi_chu`.
  - `giang_vien_id` chỉ chọn user vai trò `giang_vien`; được để trống và bổ sung sau.
  - Danh sách lọc theo học kỳ/môn/giảng viên/trạng thái; trang chi tiết hiển thị sĩ số hiện tại / tối đa.
  - Giảng viên chỉ thấy lớp HP được phân công cho mình; Admin/Giáo vụ thấy tất cả.
  - Không cho sửa lớp đã `ket_thuc` (trừ Admin); validate sĩ số 1–200.
- **Ưu tiên / SP / Sprint:** P1 · 5 · Sprint 4

#### US-17 — Ghi danh sinh viên vào lớp học phần
- **Tác nhân:** Giáo vụ, Admin
- **User Story:** Là một giáo vụ, tôi muốn ghi danh nhiều sinh viên vào lớp HP để lớp có đủ danh sách trước khi giảng viên nhập điểm.
- **Acceptance Criteria:**
  - Chọn lớp HP → thêm nhiều SV (theo lớp hành chính hoặc tìm từng SV) trong một lần.
  - Chặn ghi danh trùng (`student_id`, `course_id`, `lan_hoc`) và chặn vượt `so_sv_toi_da`.
  - Rút ghi danh được khi lớp chưa chốt điểm; SV đã có điểm không cho rút.
  - Danh sách ghi danh có tìm kiếm, phân trang và hiển thị trạng thái `dang_hoc`/`du_thi`/`hoc_lai`/`rut`.
  - Chỉ Admin/Giáo vụ ghi danh; giảng viên xem danh sách lớp mình dạy.
- **Ưu tiên / SP / Sprint:** P1 · 5 · Sprint 4

**Nhóm Báo cáo & Thông báo (Epic E8 + E9)**

#### US-26 — Xuất Excel danh sách sinh viên
- **Tác nhân:** Giáo vụ, Admin
- **User Story:** Là một giáo vụ, tôi muốn xuất danh sách sinh viên đang xem ra Excel để gửi báo cáo hoặc lưu trữ ngoài hệ thống.
- **Acceptance Criteria:**
  - Nút "Xuất Excel" xuất đúng **bộ lọc hiện tại** (không xuất toàn bộ nếu đang lọc).
  - File `.xlsx` mở được bằng Excel, tiếng Việt không lỗi font, đủ **12 cột**: mã SV, họ tên, ngày sinh, giới tính, lớp, ngành, khoa, email, SĐT, CCCD, địa chỉ, trạng thái.
  - Tên file có tiền tố `danh-sach-sinh-vien` + ngày giờ; dòng tiêu đề được in đậm và đóng băng.
  - Chỉ Admin/Giáo vụ xuất được; giảng viên/sinh viên nhận 403.
- **Ưu tiên / SP / Sprint:** P3 · 5 · Sprint 6

#### US-27 — Xuất bảng điểm Excel/PDF
- **Tác nhân:** Giáo vụ, Admin (lớp), Sinh viên (cá nhân)
- **User Story:** Là một giáo vụ/giảng viên, tôi muốn xuất bảng điểm lớp và bảng điểm cá nhân ra Excel/PDF để lưu trữ và in ấn.
- **Acceptance Criteria:**
  - Excel bảng điểm lớp gồm mã SV, họ tên, điểm QT, điểm thi, TB, chữ, hệ 4, đạt/không đạt.
  - PDF dùng font `DejaVu Sans`, hiển thị đúng dấu tiếng Việt; bảng 50 dòng không tràn trang, tự ngắt trang và lặp header ở trang 2.
  - Sinh viên chỉ xuất được bảng điểm của mình; giảng viên chỉ xuất lớp được phân công.
  - Tên file chứa mã lớp HP hoặc mã SV + học kỳ; lỗi font/tràn trang phải được test trước khi demo.
- **Ưu tiên / SP / Sprint:** P2 · 8 · Sprint 6

#### US-28 — Gửi thông báo theo nhóm người nhận
- **Tác nhân:** Giáo vụ, Admin
- **User Story:** Là một giáo vụ, tôi muốn gửi thông báo tới toàn trường/khoa/lớp để phổ biến lịch thi, học vụ và các thay đổi kịp thời.
- **Acceptance Criteria:**
  - Soạn `tieu_de`, `noi_dung` (bắt buộc, giới hạn độ dài hợp lý) và chọn đối tượng: toàn trường / Khoa / Lớp hành chính / Lớp HP / một người dùng.
  - Trước khi gửi hiển thị **số người nhận dự kiến** để tránh gửi nhầm.
  - Lưu bảng `announcements` + `announcement_user`; chỉ người thuộc đối tượng thấy thông báo.
  - Người gửi, thời gian gửi được lưu lại; có thể xoá thông báo đã gửi (Admin) kèm xác nhận.
- **Ưu tiên / SP / Sprint:** P3 · 5 · Sprint 6

### 2.4. Giảng viên

#### US-18 — Nhập điểm quá trình & điểm thi
- **Tác nhân:** Giảng viên (lớp được phân công), Giáo vụ/Admin hỗ trợ
- **User Story:** Là một giảng viên, tôi muốn nhập điểm quá trình và điểm thi cho cả lớp trên một bảng duy nhất để hoàn tất điểm cuối kỳ nhanh chóng.
- **Acceptance Criteria:**
  - Chọn lớp HP được phân công → hiển thị danh sách SV đã ghi danh kèm ô nhập QT/Thi.
  - Điểm nhập trong khoảng **0–10**, tối đa 2 chữ số thập phân; để trống nếu chưa có điểm.
  - Lưu hàng loạt trong một transaction; dòng lỗi được báo rõ và không làm mất điểm các dòng hợp lệ.
  - Sau khi lưu, điểm TB/chữ/hệ 4/đạt được tính tự động và hiển thị ngay.
  - Giảng viên chỉ sửa lớp mình dạy; lớp đã chốt điểm chỉ đọc; mọi thao tác ghi nhận `nguoi_nhap_id`, `nhap_luc`.
- **Ưu tiên / SP / Sprint:** P1 · 8 · Sprint 4

#### US-19 — Tự động tính điểm TB, điểm chữ, hệ 4
- **Tác nhân:** Giảng viên, Giáo vụ (hưởng lợi)
- **User Story:** Là một giảng viên, tôi muốn hệ thống tự tính điểm TB, điểm chữ, hệ 4 và đạt/không đạt để không phải tra bảng thủ công và tránh sai sót.
- **Acceptance Criteria:**
  - Công thức `Điểm TB = QT × 0.4 + Thi × 0.6`, đọc từ `config/diem.php`, làm tròn 2 chữ số.
  - Quy đổi theo ngưỡng: TB ≥ 8.5 → A (4.0); ≥ 8.0 → B+ (3.5); ≥ 7.0 → B (3.0); ≥ 6.5 → C+ (2.5); ≥ 5.5 → C (2.0); ≥ 5.0 → D+ (1.5); ≥ 4.0 → D (1.0); < 4.0 → F (0.0).
  - `dat = true` khi điểm TB ≥ 4.0; thiếu một trong hai điểm thì các cột tính toán để `null`.
  - Cập nhật QT/Thi sẽ tính lại ngay; Unit Test tối thiểu 7 vector, gồm các mốc biên 4.0 / 5.0 / 6.5 / 8.5.
  - Không hard-code trọng số/thang điểm trong controller hay view.
- **Ưu tiên / SP / Sprint:** P1 · 5 · Sprint 4

#### US-20 — Chốt điểm & chống sửa sau chốt
- **Tác nhân:** Giảng viên, Giáo vụ/Admin
- **User Story:** Là một giảng viên, tôi muốn chốt điểm sau khi nhập xong để kết quả được công bố và không bị sửa ngoài ý muốn.
- **Acceptance Criteria:**
  - Nút "Chốt điểm" có hộp thoại xác nhận; khi chốt ghi `khoa_luc` cho toàn bộ điểm của lớp.
  - Sau khi chốt, mọi cố gắng sửa điểm trả về 403/422 kèm thông báo tiếng Việt; form chuyển sang chỉ đọc.
  - Chỉ Admin/Giáo vụ được mở lại, bắt buộc nhập lý do; lưu vết người mở và thời điểm.
  - Sinh viên chỉ thấy điểm của lớp đã chốt (điểm chưa chốt không hiển thị ở cổng sinh viên).
  - Có test: GV sửa lớp người khác bị chặn; sửa sau chốt bị chặn; mở lại rồi sửa thành công.
- **Ưu tiên / SP / Sprint:** P2 · 3 · Sprint 4

#### US-22 — Theo dõi lớp chưa nhập điểm
- **Tác nhân:** Giảng viên
- **User Story:** Là một giảng viên, tôi muốn thấy lớp nào chưa nhập/chưa chốt điểm để không bỏ sót hạn nộp điểm.
- **Acceptance Criteria:**
  - Danh sách lớp HP được phân công trong học kỳ hiện tại kèm trạng thái: **Chưa nhập / Đang nhập / Đã chốt**.
  - Hiển thị số SV đã có điểm / tổng SV của lớp.
  - Bấm vào lớp → mở đúng màn nhập điểm; chỉ hiển thị lớp của giảng viên đang đăng nhập.
- **Ưu tiên / SP / Sprint:** P3 · 3 · Sprint 5

#### US-24 — Dashboard giảng viên
- **Tác nhân:** Giảng viên
- **User Story:** Là một giảng viên, tôi muốn có dashboard tóm tắt lớp dạy, sĩ số và việc nhập điểm để bắt đầu công việc nhanh mỗi ngày.
- **Acceptance Criteria:**
  - Card: số lớp đang dạy, tổng SV, số lớp chưa nhập điểm, số lớp đã chốt điểm.
  - Có lối tắt đến "Nhập điểm" của từng lớp và danh sách lớp đang dạy.
  - Chỉ hiển thị dữ liệu lớp được phân công; không lộ lớp/điểm của giảng viên khác.
  - Trang tải < 1,5 giây với dữ liệu seed.
- **Ưu tiên / SP / Sprint:** P3 · 3 · Sprint 5

### 2.5. Sinh viên

#### US-21 — Tra cứu, in bảng điểm & GPA
- **Tác nhân:** Sinh viên
- **User Story:** Là một sinh viên, tôi muốn xem bảng điểm và GPA của mình theo từng học kỳ để theo dõi kết quả học tập mà không cần hỏi giáo vụ.
- **Acceptance Criteria:**
  - Chỉ xem dữ liệu của chính mình; sửa URL sang sinh viên khác bị chặn (test IDOR).
  - Bảng điểm gồm môn, số tín chỉ, điểm QT, thi, TB, chữ, hệ 4, đạt/không đạt; lọc theo học kỳ và xem tất cả.
  - GPA học kỳ = `Σ(điểm hệ 4 × số tín chỉ) / Σ(số tín chỉ có điểm)`; GPA tích luỹ tính trên mọi môn đã có điểm.
  - Hiển thị tổng tín chỉ đạt và xếp loại: Xuất sắc ≥ 3.60 · Giỏi ≥ 3.20 · Khá ≥ 2.50 · Trung bình ≥ 2.00 · Yếu < 2.00.
  - Môn học lại lấy điểm **cao nhất** trong các lần học; chỉ hiển thị điểm đã chốt.
  - Có nút "In bảng điểm" với CSS in gọn (ẩn menu, giữ header trường/lớp).
- **Ưu tiên / SP / Sprint:** P1 · 8 · Sprint 5

#### US-25 — Dashboard sinh viên
- **Tác nhân:** Sinh viên
- **User Story:** Là một sinh viên, tôi muốn thấy GPA, tín chỉ đạt và thông báo mới trên trang chủ để biết ngay tình hình học tập của mình.
- **Acceptance Criteria:**
  - Hiển thị GPA tích luỹ, tổng tín chỉ đạt, số điểm mới được công bố trong 7 ngày, xếp loại học tập.
  - Danh sách 5 điểm mới nhất kèm liên kết đến bảng điểm đầy đủ.
  - Chỉ dữ liệu của sinh viên đang đăng nhập; không lộ thông tin lớp/điểm của người khác.
- **Ưu tiên / SP / Sprint:** P3 · 3 · Sprint 5

#### US-29 — Hộp thư thông báo & badge chưa đọc
- **Tác nhân:** Sinh viên (áp dụng cho mọi vai trò)
- **User Story:** Là một sinh viên, tôi muốn xem danh sách thông báo và biết có bao nhiêu thông báo chưa đọc để không bỏ lỡ thông tin học vụ.
- **Acceptance Criteria:**
  - Danh sách thông báo chỉ gồm thông báo gửi cho mình (theo lớp/khoa/toàn trường), sắp xếp mới nhất trước, có phân trang.
  - Thông báo chưa đọc được in đậm; badge số lượng chưa đọc hiển thị trên topbar.
  - Mở một thông báo → đánh dấu đã đọc và badge giảm sau khi tải lại; có nút "Đánh dấu tất cả đã đọc".
  - Không đọc được thông báo của người khác bằng cách sửa ID (test IDOR).
  - Dashboard của người dùng hiển thị 5 thông báo mới nhất và badge chưa đọc sau khi E9 hoàn thành.
- **Ưu tiên / SP / Sprint:** P3 · 5 · Sprint 6

---

## 3. Product Backlog (sắp xếp theo ưu tiên & sprint)

| STT | Mã US | Epic | Tên User Story | Tác nhân | Ưu tiên | SP | Sprint dự kiến |
|---:|---|---|---|---|---|---:|---|
| 1 | US-01 | E1 | Đăng nhập / đăng xuất | Khách, mọi vai trò | P1 | 5 | Sprint 1 |
| 2 | US-02 | E1 | Kiểm soát truy cập theo vai trò | Admin, mọi vai trò | P1 | 3 | Sprint 1 |
| 3 | US-03 | E1 | Quản lý tài khoản người dùng | Admin | P1 | 8 | Sprint 1 |
| 4 | US-04 | E1 | Hồ sơ cá nhân & đổi mật khẩu | Mọi người dùng | P2 | 3 | Sprint 1 |
| 5 | US-05 | E2 | Quản lý Khoa | Giáo vụ, Admin | P1 | 3 | Sprint 2 |
| 6 | US-06 | E2 | Quản lý Ngành | Giáo vụ, Admin | P1 | 3 | Sprint 2 |
| 7 | US-07 | E2 | Quản lý Lớp hành chính | Giáo vụ, Admin | P1 | 3 | Sprint 2 |
| 8 | US-08 | E4 | Quản lý Môn học | Giáo vụ, Admin | P2 | 3 | Sprint 2 |
| 9 | US-09 | E4 | Quản lý Học kỳ | Giáo vụ, Admin | P2 | 2 | Sprint 2 |
| 10 | US-10 | E2+E4 | Tra cứu & bảo vệ ràng buộc dữ liệu danh mục | Giáo vụ, Admin | P3 | 3 | Sprint 2 |
| 11 | US-11 | E3 | Quản lý hồ sơ sinh viên | Giáo vụ, Admin | P1 | 8 | Sprint 3 |
| 12 | US-12 | E3 | Nhập sinh viên hàng loạt từ Excel | Giáo vụ, Admin | P1 | 8 | Sprint 3 |
| 13 | US-13 | E3 | Tìm kiếm, lọc & phân trang sinh viên | Giáo vụ, Admin | P2 | 5 | Sprint 3 |
| 14 | US-14 | E3 | Xoá mềm & khôi phục sinh viên | Giáo vụ, Admin | P3 | 3 | Sprint 3 |
| 15 | US-15 | E3 | Cấp tài khoản đăng nhập cho sinh viên | Giáo vụ, Admin | P2 | 3 | Sprint 3 |
| 16 | US-16 | E5 | Mở lớp học phần & phân công giảng viên | Giáo vụ, Admin | P1 | 5 | Sprint 4 |
| 17 | US-17 | E5 | Ghi danh sinh viên vào lớp học phần | Giáo vụ, Admin | P1 | 5 | Sprint 4 |
| 18 | US-18 | E6 | Nhập điểm quá trình & điểm thi | Giảng viên | P1 | 8 | Sprint 4 |
| 19 | US-19 | E6 | Tự động tính điểm TB, điểm chữ, hệ 4 | Giảng viên | P1 | 5 | Sprint 4 |
| 20 | US-20 | E6 | Chốt điểm & chống sửa sau chốt | Giảng viên, Admin | P2 | 3 | Sprint 4 |
| 21 | US-21 | E6 | Tra cứu, in bảng điểm & GPA | Sinh viên | P1 | 8 | Sprint 5 |
| 22 | US-22 | E6 | Theo dõi lớp chưa nhập điểm | Giảng viên | P3 | 3 | Sprint 5 |
| 23 | US-23 | E7 | Dashboard tổng quan Admin/Giáo vụ | Admin, Giáo vụ | P2 | 8 | Sprint 5 |
| 24 | US-24 | E7 | Dashboard giảng viên | Giảng viên | P3 | 3 | Sprint 5 |
| 25 | US-25 | E7 | Dashboard sinh viên | Sinh viên | P3 | 3 | Sprint 5 |
| 26 | US-26 | E8 | Xuất Excel danh sách sinh viên | Giáo vụ, Admin | P3 | 5 | Sprint 6 |
| 27 | US-27 | E8 | Xuất bảng điểm Excel/PDF | Giáo vụ, Admin, Sinh viên | P2 | 8 | Sprint 6 |
| 28 | US-28 | E9 | Gửi thông báo theo nhóm người nhận | Giáo vụ, Admin | P3 | 5 | Sprint 6 |
| 29 | US-29 | E9 | Hộp thư thông báo & badge chưa đọc | Mọi người dùng | P3 | 5 | Sprint 6 |
| **Tổng** | **29 US** | **E1–E9** | | | | **137 SP** | **Sprint 1–6** |

> **Nguyên tắc sắp xếp:** ưu tiên theo giá trị nghiệp vụ + phụ thuộc dữ liệu. Vì vậy một số story P2/P3 ở sprint sớm (ví dụ US-08, US-10) đứng trước story P1 ở sprint sau (US-21) do US-21 cần có điểm đã nhập/chốt.

---

## 4. Tổng quan kế hoạch Sprint

| Sprint | Thời lượng | Sprint Goal (mục tiêu 1 câu) | User Story | SP | Giờ (*) | Demo cuối sprint |
|---|---|---|---:|---:|---:|---|
| **Sprint 0** | 1 tuần | Có nền tảng kỹ thuật: asset build, layout Tailwind, i18n tiếng Việt, test pipeline xanh | EN-01 → EN-09 | – | 36,5 | `composer run dev` → layout + trang 404 tiếng Việt, test xanh |
| **Sprint 1** | 2 tuần | 4 vai trò đăng nhập được, route được bảo vệ, Admin quản lý tài khoản | US-01 → US-04 | 19 | 68 | 4 tài khoản demo đăng nhập; SV bị 403 khi vào `/users` |
| **Sprint 2** | 2 tuần | Chuẩn hoá dữ liệu gốc: Khoa → Ngành → Lớp, Môn học, Học kỳ | US-05 → US-10 | 17 | 73 | CRUD 5 danh mục; chặn xoá Khoa còn Ngành |
| **Sprint 3** | 2 tuần | Quản lý hồ sơ sinh viên: CRUD, ảnh, import Excel, tìm kiếm/lọc | US-11 → US-15 | 27 | 78 | Import 20 dòng (3 lỗi), đổi ảnh, xoá mềm & khôi phục |
| **Sprint 4** | 2 tuần | Lớp HP + ghi danh + nhập điểm, tự tính TB/chữ/hệ 4 và chốt điểm | US-16 → US-20 | 26 | 84 | GV nhập điểm 1 lớp → TB/chữ đúng; chốt điểm chặn sửa |
| **Sprint 5** | 2 tuần | SV tra cứu điểm/GPA; dashboard thống kê trực quan | US-21 → US-25 | 25 | 69 | SV in bảng điểm; Admin xem 4 biểu đồ |
| **Sprint 6** | 2 tuần | Báo cáo Excel/PDF, thông báo, hardening, tài liệu & bàn giao | US-26 → US-29 | 23 | 105 | Xuất PDF/Excel, gửi thông báo theo lớp, demo hội đồng |
| **TỔNG** | **~13 tuần** | | **29 US** | **137** | **513,5** | ≈ **85,6 ngày công** (6 h/ngày) |

> (*) Số giờ lấy từ `DANH_SACH_TASK_SPRINT.csv` và `SPRINT_CHI_TIET.md`, đã gồm công test + tài liệu + buffer. Sprint 6 có ~35–40 h cho UAT, hardening, tài liệu và slide nên SP thấp hơn nhưng giờ cao.
>
> **Năng lực tham chiếu:** 1 người × 6 h/ngày × 10 ngày = **60 h/2 tuần**. Các sprint 68–105 h cần **2 người** hoặc áp dụng quy tắc cắt scope ở mục 5.8. Sprint 0 chỉ 1 tuần nên 36,5 h là phù hợp.

---

## 5. Sprint Backlog chi tiết

### 5.1. Sprint 0 — Nền tảng kỹ thuật (1 tuần · 36,5 h)

**Sprint Goal:** *"Chạy được app ở localhost với layout Tailwind, giao diện tiếng Việt, test pipeline xanh — sẵn sàng cho Sprint 1."*

| Mã | Công việc chi tiết | Loại | Người thực hiện | Ước lượng | Kết quả / AC |
|---|---|---|---:|---:|---|
| EN-01 | Khởi động Laragon, xác nhận PHP 8.3 / Composer / Node | Infra | DevOps | 1 h | `php -v`, `composer -V`, `node -v` đạt yêu cầu |
| EN-02 | `npm install` + `npm run build` | Infra | DevOps | 1 h | Có `public/build`, Tailwind render đúng |
| EN-03 | Cấu hình `.env` locale `vi`, timezone `Asia/Ho_Chi_Minh`, tạo `lang/vi` | Config | Backend | 4 h | `php artisan about` hiển thị `vi`; lỗi validate tiếng Việt |
| EN-04 | `storage:link` + thư mục ảnh sinh viên | Infra | Backend | 0,5 h | `public/storage` tồn tại, ghi/đọc ảnh được |
| EN-05 | Layout app/guest + sidebar + topbar + flash | FE | Frontend | 8 h | Trang chủ render layout đúng, responsive |
| EN-06 | Bộ component Blade tái sử dụng (button, input, card, table…) | FE | Frontend | 6 h | Component render đúng style, dùng lại ở S1–S6 |
| EN-07 | Khung route + trang lỗi 403/404/419/429/500 tiếng Việt | BE/FE | Backend | 4 h | `/khong-ton-tai` hiện 404 tiếng Việt có nút về trang chủ |
| EN-08 | Enum nền + `config/diem.php` + `config/qlsv.php` | BE | Backend | 5 h | Enum có label tiếng Việt; đọc được bằng `config()` |
| EN-09 | Git branch + GitHub Projects + HealthTest + README | Docs/Test | DevOps/Tester | 7 h | `php artisan test` xanh; import đủ task vào bảng sprint |
| **TỔNG** | | | | **36,5 h** | |

**Demo Sprint 0 (5 phút):** `composer run dev` → mở `http://127.0.0.1:8000` thấy layout → nhập URL sai thấy 404 tiếng Việt → `php artisan about` hiển thị locale/timezone → kéo EN-01…EN-09 sang Done.

### 5.2. Sprint 1 — Sprint Backlog chi tiết (2 tuần · 68 h)

**Sprint Goal:** *"4 vai trò (Admin / Giáo vụ / Giảng viên / Sinh viên) đăng nhập được, mọi route nghiệp vụ được bảo vệ, Admin quản lý được tài khoản."*

| Mã | Công việc chi tiết (Tasks) | Thuộc US | Người thực hiện | Ước lượng |
|---|---|---|---:|---:|
| TS1-01 | Migration bổ sung `role`, `trang_thai`, `so_dien_thoai`, `avatar`, `last_login_at` cho `users` | US-01, US-03 | Backend Dev | 2 h |
| TS1-02 | Enum `Role` + nâng cấp model `User` (cast, scope, `hasRole()`) | US-01, US-02, US-03 | Backend Dev | 3 h |
| TS1-03 | Middleware `EnsureUserHasRole` + alias `role` trong `bootstrap/app.php` | US-02 | Backend Dev | 3 h |
| TS1-04 | Đăng nhập/đăng xuất + rate limit 5 lần/phút + redirect theo vai trò | US-01 | Backend Dev | 6 h |
| TS1-05 | Views login, dashboard 4 biến thể, sidebar/topbar động | US-01, US-04 | Frontend Dev | 7 h |
| TS1-06 | `UserController` CRUD + reset mật khẩu + khoá/mở + `UserPolicy` | US-03 | Backend Dev | 15 h |
| TS1-07 | Views quản lý tài khoản (index/create/edit) + hồ sơ cá nhân | US-03, US-04 | Frontend Dev | 12 h |
| TS1-08 | Hồ sơ cá nhân, đổi mật khẩu, upload avatar | US-04 | Backend Dev | 4 h |
| TS1-09 | Seeder 4 tài khoản demo theo vai trò | US-01 | Backend Dev | 3 h |
| TS1-10 | Feature Test: đăng nhập, phân quyền 4 vai trò, quản lý tài khoản | US-01 → US-04 | Tester | 10 h |
| TS1-11 | Đóng gói bản demo, cập nhật backlog & nhật ký sprint | Tất cả | DevOps/Lead | 3 h |
| **TỔNG** | | | | **68 h** |

**Kịch bản demo Sprint 1 (10 phút):**
1. Đăng nhập lần lượt 4 tài khoản demo → thấy 4 dashboard khác nhau.
2. Sinh viên gõ `/users` → nhận 403 tiếng Việt.
3. Admin tạo tài khoản giảng viên mới → reset mật khẩu → khoá tài khoản → thử đăng nhập bị chặn.
4. Đổi mật khẩu cá nhân → đăng nhập lại bằng mật khẩu mới.
5. Chạy `php artisan test` → toàn bộ xanh.

### 5.3. Sprint 2 — Danh mục (2 tuần · 73 h)

**Sprint Goal:** *"Chuẩn hoá toàn bộ dữ liệu gốc để Sprint 3–5 chỉ việc tham chiếu khoá ngoại."*

| Mã | Nhóm công việc | Thuộc US | Người thực hiện | Ước lượng |
|---|---|---|---:|---:|
| S2-01 → S2-05 | Migration + Model 5 danh mục: Khoa, Ngành, Lớp (`SchoolClass`), Môn học, Học kỳ | US-05 → US-09 | Backend Dev | 14 h |
| S2-06 | Factory + Seeder dữ liệu mẫu 5 danh mục | US-10 | Backend Dev | 6 h |
| S2-07 | Form Request × 5 (validate + unique + tiếng Việt) | US-05 → US-09 | Backend Dev | 6 h |
| S2-08 | Controller × 5 (CRUD + tìm kiếm/lọc + phân trang) | US-05 → US-10 | Backend Dev | 14 h |
| S2-09 | Views × 5 module (index/create/edit/_form) | US-05 → US-10 | Frontend Dev | 16 h |
| S2-10 | Route nhóm `danh-muc` + menu sidebar | US-10 | Backend/Frontend | 3 h |
| S2-11 | Chặn xoá danh mục còn dữ liệu con + thông báo thân thiện | US-10 | Backend Dev | 4 h |
| S2-12 | Feature Test 5 module + phân quyền | US-10 | Tester | 10 h |
| **TỔNG** | | | | **73 h** |

**Demo Sprint 2 (10 phút):** Tạo Khoa → Ngành → Lớp → Môn → Học kỳ; sửa và tìm kiếm từng danh mục; thử xoá Khoa đang có Ngành → bị chặn kèm thông báo; đăng nhập giảng viên → không thấy menu Danh mục.

### 5.4. Sprint 3 — Quản lý Sinh viên (2 tuần · 78 h)

**Sprint Goal:** *"Giáo vụ thay thế hoàn toàn file Excel thủ công: thêm/sửa/tra cứu hồ sơ SV, import hàng loạt, có ảnh đại diện và tìm kiếm/lọc đa tiêu chí."*

| Mã | Nhóm công việc | Thuộc US | Người thực hiện | Ước lượng |
|---|---|---|---:|---:|
| S3-01 | Cài & kiểm tra `maatwebsite/excel` (có phương án B: CSV) | US-12 | DevOps | 2 h |
| S3-02 | Migration + Model `students` (soft delete, quan hệ) | US-11, US-14 | Backend Dev | 4 h |
| S3-03 | Enum `GioiTinh`, `TrangThaiSinhVien` + scope lọc | US-13 | Backend Dev | 5 h |
| S3-04 | Form Request store/update + validate ảnh | US-11 | Backend Dev | 5 h |
| S3-05 | `StudentService`: upload/xoá ảnh, tạo tài khoản SV | US-11, US-15 | Backend Dev | 6 h |
| S3-06 | `StudentController`: CRUD + soft delete + khôi phục | US-11, US-14 | Backend Dev | 8 h |
| S3-07 | Import Excel + file mẫu + báo lỗi theo dòng | US-12 | Backend Dev | 14 h |
| S3-08 | Views: index/filter, create/edit/_form, show, import, trash | US-11 → US-14 | Frontend Dev | 18 h |
| S3-09 | Route nhóm `sinh-vien` + menu | US-11 → US-15 | Backend/Frontend | 3 h |
| S3-10 | Feature/Unit Test module sinh viên | US-11 → US-15 | Tester | 13 h |
| **TỔNG** | | | | **78 h** |

**Demo Sprint 3 (10 phút):** Import file 20 dòng (có 3 dòng lỗi) → xem báo lỗi đúng dòng/cột; sửa 1 SV + đổi ảnh; lọc theo Khoa/Ngành/Lớp/trạng thái; xoá mềm → khôi phục; tạo tài khoản SV và đăng nhập thử.

### 5.5. Sprint 4 — Lớp học phần & Nhập điểm (2 tuần · 84 h)

**Sprint Goal:** *"Giảng viên nhập điểm QT/Thi cho lớp mình dạy, hệ thống tự tính Điểm TB – Điểm chữ – Hệ 4 – Đạt/Không đạt và chốt điểm để chống sửa."*

| Mã | Nhóm công việc | Thuộc US | Người thực hiện | Ước lượng |
|---|---|---|---:|---:|
| S4-01 | Migration `courses` + `enrollments` + `grades` | US-16 → US-18 | Backend Dev | 5 h |
| S4-02 | `config/diem.php` + `DiemService` | US-19 | Backend Dev | 5 h |
| S4-03 | Unit Test `DiemService` với 7 vector điểm | US-19 | Tester | 4 h |
| S4-04 | Model + quan hệ + `CoursePolicy` + `GradeObserver` | US-16, US-18, US-20 | Backend Dev | 6 h |
| S4-05 | `CourseController` CRUD + show + chốt/mở lớp HP | US-16, US-20 | Backend Dev | 10 h |
| S4-06 | Ghi danh SV vào lớp HP (thêm nhiều + rút) | US-17 | Backend Dev | 8 h |
| S4-07 | Nhập điểm hàng loạt theo lớp (transaction) | US-18 | Backend Dev | 10 h |
| S4-08 | Views courses/enrollments/grades edit | US-16 → US-18 | Frontend Dev | 18 h |
| S4-09 | Route nhóm `lop-hoc-phan` + menu theo vai trò | US-16 | Backend/Frontend | 3 h |
| S4-10 | Feature Test quyền GV, ghi danh, nhập điểm, chốt điểm | US-16 → US-20 | Tester | 15 h |
| **TỔNG** | | | | **84 h** |

**Demo Sprint 4 (10 phút):** Tạo lớp HP + phân công GV → ghi danh 10 SV → GV nhập QT/Thi → kiểm tra TB/chữ/hệ 4 đúng công thức → chốt điểm → thử sửa bị chặn → Admin mở lại kèm lý do.

### 5.6. Sprint 5 — Tra cứu điểm/GPA & Dashboard (2 tuần · 69 h)

**Sprint Goal:** *"Sinh viên tự xem/in được bảng điểm & GPA của mình; Ban giám hiệu/giáo vụ thấy dashboard trực quan thay cho việc đếm tay trên Excel."*

| Mã | Nhóm công việc | Thuộc US | Người thực hiện | Ước lượng |
|---|---|---|---:|---:|
| S5-01 | `GpaService` tính GPA học kỳ, tích luỹ, xếp loại | US-21 | Backend Dev | 6 h |
| S5-02 | Unit Test `GpaService` | US-21 | Tester | 4 h |
| S5-03 | Cổng sinh viên xem & in bảng điểm | US-21 | Backend/Frontend | 8 h |
| S5-04 | Trang chi tiết sinh viên kèm bảng điểm (giáo vụ) | US-21 | Backend/Frontend | 4 h |
| S5-05 | `ThongKeService` tổng hợp số liệu + cache 5 phút | US-23 | Backend Dev | 9 h |
| S5-06 | Dashboard Admin/Giáo vụ: 4 card + 4 biểu đồ | US-23 | Backend/Frontend | 10 h |
| S5-07 | Dashboard Giảng viên & Sinh viên | US-22, US-24, US-25 | Backend/Frontend | 6 h |
| S5-08 | Cài Chart.js + entry Vite `dashboard.js` | US-23 | Frontend Dev | 8 h |
| S5-09 | Tối ưu query & bổ sung index | US-23 | Backend Dev | 6 h |
| S5-10 | Feature/Unit Test dashboard & quyền xem điểm | US-21 → US-25 | Tester | 8 h |
| **TỔNG** | | | | **69 h** |

**Demo Sprint 5 (10 phút):** SV đăng nhập → xem bảng điểm theo kỳ + GPA + tín chỉ đạt → in bảng điểm; thử sửa URL sang SV khác → 403; Admin xem dashboard 4 card + 4 biểu đồ; GV thấy lớp chưa nhập điểm.

### 5.7. Sprint 6 — Báo cáo, Thông báo & Bàn giao (2 tuần · 105 h)

**Sprint Goal:** *"Có báo cáo Excel/PDF chuẩn, thông báo nội bộ hoạt động, hệ thống được kiểm thử & tối ưu, tài liệu + kịch bản bảo vệ sẵn sàng."*

| Mã | Nhóm công việc | Thuộc US | Người thực hiện | Ước lượng |
|---|---|---|---:|---:|
| S6-01 | Export Excel danh sách sinh viên theo bộ lọc | US-26 | Backend Dev | 8 h |
| S6-02 | Export Excel bảng điểm lớp & cá nhân | US-27 | Backend Dev | 6 h |
| S6-03 | Cài DomPDF + cấu hình font tiếng Việt | US-27 | DevOps | 3 h |
| S6-04 | PDF bảng điểm & danh sách sinh viên | US-27 | Backend/Frontend | 10 h |
| S6-05 | Migration + service gửi thông báo theo đối tượng | US-28 | Backend Dev | 10 h |
| S6-06 | Màn quản lý thông báo + hộp thư + badge chưa đọc | US-28, US-29 | Backend/Frontend | 12 h |
| S6-07 | UAT 4 vai trò: responsive, bảo mật, IDOR, 403 | US-01 → US-29 | Tester | 12 h |
| S6-08 | Hardening & tối ưu cuối (`optimize`, Pint, log) | – | Backend Dev | 8 h |
| S6-09 | Dữ liệu demo + script backup DB | – | Backend Dev | 6 h |
| S6-10 | Tài liệu bàn giao: README, ERD, hướng dẫn, nhật ký sprint | – | Docs | 12 h |
| S6-11 | Slide bảo vệ + kịch bản demo + Retro | – | Lead | 8 h |
| S6-12 | Buffer sửa bug phát sinh từ UAT | – | Cả team | 10 h |
| **TỔNG** | | | | **105 h** |

**Demo Sprint 6 (12–15 phút):** `migrate:fresh --seed` → đăng nhập admin → xuất Excel danh sách SV → xuất PDF bảng điểm lớp → gửi thông báo cho 1 lớp → SV đăng nhập đọc thông báo + xem điểm → chạy `php artisan test` và `pint --test`.

### 5.8. Quy tắc điều chỉnh phạm vi khi trễ

- Hết **tuần 1** của sprint mà task ưu tiên **P1 còn > 50%** ⇒ cắt các task **P3** và ghi rõ "cắt" vào nhật ký sprint, chuyển sang sprint sau.
- Thứ tự bảo toàn: **E1 → E2/E4 → E3 → E6 (nhập điểm)**; **E7/E8/E9** có thể hạ xuống mức "Nên có".
- Phương án cắt cụ thể:
  - **Sprint 1:** bỏ trang `show` hồ sơ (đã gộp vào form sửa); giữ đủ 4 vai trò + phân quyền.
  - **Sprint 2:** gộp view create/edit; bỏ trang `show` của Khoa; giữ đủ CRUD 5 danh mục.
  - **Sprint 3:** bỏ preview ảnh, bỏ import gán sinh viên hàng loạt; vẫn giữ import Excel + báo lỗi theo dòng.
  - **Sprint 4:** nếu trễ, chuyển **US-20 (chốt điểm)** sang Sprint 5; vẫn phải xong nhập điểm + tính điểm.
  - **Sprint 5:** giảm từ 4 biểu đồ xuống 3 (ưu tiên SV theo Khoa, đạt/rớt, điểm TB theo kỳ).
  - **Sprint 6:** ưu tiên Excel/PDF/thông báo cơ bản; tài liệu làm song song từ Sprint 5; dùng 10 h buffer S6-12 cho bug chặn demo.
- Mọi yêu cầu mới ngoài phạm vi ⇒ ghi vào **Backlog giai đoạn sau** (mục 7), **không chèn** vào sprint đang chạy.

---

## 6. Sản phẩm đạt được sau từng Sprint (lộ trình phát hành)

| Mốc | Thời điểm | Sản phẩm bàn giao (Increment) | Bằng chứng demo |
|---|---|---|---|
| **Cuối Sprint 0** | Tuần 1 | Khung ứng dụng Laravel 13 chạy localhost, layout Tailwind, giao diện tiếng Việt, test pipeline xanh | Mở trang chủ + trang 404; `php artisan test` pass |
| **Cuối Sprint 1** | Tuần 3 | 4 vai trò đăng nhập được; phân quyền route/menu; Admin quản lý tài khoản; hồ sơ cá nhân | 4 tài khoản demo; SV bị 403 ở `/users`; reset/khoá tài khoản |
| **Cuối Sprint 2** | Tuần 5 | CRUD hoàn chỉnh 5 danh mục Khoa – Ngành – Lớp – Môn – Học kỳ; chặn xoá dữ liệu đang dùng | Tạo chuỗi Khoa → Ngành → Lớp; xoá Khoa còn Ngành bị chặn |
| **Cuối Sprint 3** | Tuần 7 | Hồ sơ sinh viên đầy đủ, ảnh đại diện, import Excel, tìm kiếm/lọc, xoá mềm & khôi phục, tài khoản SV | Import 20 dòng có báo lỗi theo dòng; lọc đa tiêu chí |
| **Cuối Sprint 4** | Tuần 9 | Lớp học phần, ghi danh, nhập điểm QT/Thi, tự tính TB/chữ/hệ 4/đạt, chốt điểm chống sửa | GV nhập điểm 1 lớp; chốt điểm → sửa bị chặn |
| **Cuối Sprint 5** | Tuần 11 | Cổng sinh viên tra cứu/in bảng điểm & GPA; dashboard Admin/Giáo vụ/Giảng viên/Sinh viên | SV in bảng điểm; 4 biểu đồ dashboard; GV thấy lớp chưa nhập điểm |
| **Cuối Sprint 6** | Tuần 13 | Báo cáo Excel/PDF, thông báo nội bộ, UAT & hardening, tài liệu bàn giao, slide bảo vệ | Xuất Excel/PDF; gửi thông báo theo lớp; UAT 4 vai trò; demo hội đồng |

> **Định nghĩa "phát hành" trong đồ án:** mỗi cuối sprint phải có một **increment chạy được** trên môi trường local/staging, không chỉ là code đã merge. Không demo bằng dữ liệu giả lập trong slide.

---

## 7. Backlog giai đoạn sau (ngoài MVP)

| STT | Hạng mục | Giá trị mang lại | Ưu tiên | Gợi ý thời điểm |
|---:|---|---|---|---|
| 1 | Đăng nhập Google + xác thực 2 lớp (2FA) | Tăng bảo mật, giảm quên mật khẩu | P4 | Sau Sprint 6 |
| 2 | Quên mật khẩu / gửi mật khẩu qua email | Tự phục vụ, giảm tải giáo vụ | P4 | Khi có cấu hình mail |
| 3 | Thanh toán học phí online | Số hoá nghiệp vụ tài chính | P4 | Giai đoạn 2 |
| 4 | Đăng ký tín chỉ/tự chọn môn | Mở rộng nghiệp vụ đào tạo | P4 | Giai đoạn 2 |
| 5 | Điểm danh QR / điểm danh theo buổi | Theo dõi chuyên cần | P4 | Giai đoạn 2 |
| 6 | Ứng dụng mobile cho sinh viên | Tra cứu điểm/thông báo mọi lúc | P4 | Giai đoạn 3 |
| 7 | Audit log đầy đủ mọi thao tác | Truy vết thay đổi điểm/dữ liệu | P4 | Giai đoạn 2 |
| 8 | Báo cáo động theo ma trận tùy chọn | Phân tích linh hoạt cho lãnh đạo | P4 | Giai đoạn 2 |
| 9 | Nhắc hạn nhập điểm qua email/Zalo | Giảm chậm tiến độ nhập điểm | P4 | Giai đoạn 2 |
| 10 | Xuất học bạ theo mẫu chuẩn | In ấn chính thức | P4 | Giai đoạn 3 |

**Quy tắc:** các mục P4 **không** được đưa vào Sprint 0–6. Nếu muốn làm, phải tạo US mới, ước lượng lại SP và thêm sprint mới trong tài liệu này.

---

## 8. Rủi ro & giả định

### 8.1. Rủi ro chính

| Rủi ro | Mức | Sprint liên quan | Giảm thiểu |
|---|---|---|---|
| Phình phạm vi (thêm tính năng ngoài MVP) | Cao | Tất cả | Mọi yêu cầu mới ghi vào mục 7; không chèn vào sprint đang chạy |
| Ước lượng vượt năng lực (68–105 h/sprint) | Cao | S1–S6 | Làm 2 người hoặc áp dụng quy tắc cắt scope mục 5.8; ưu tiên P1 |
| Sai công thức/thang điểm so với quy chế | Cao | S4–S5 | Chốt ADR-04 trước Sprint 4; công thức trong `config/diem.php`; 7 vector test |
| Package Excel/DomPDF không tương thích Laravel 13 | Cao | S3, S6 | Cài & test ngay **đầu sprint**; phương án B: CSV + in từ trình duyệt |
| PDF lỗi font tiếng Việt | Cao | S6 | Dùng `DejaVu Sans`; test S6-04 ngay, không để ngày cuối |
| Lỗi phân quyền/IDOR | Cao | S1–S6 | Policy + middleware + test 403/IDOR cho từng module |
| Import Excel sai định dạng | Trung bình | S3 | File mẫu, validate từng dòng, báo số dòng/cột, transaction |
| Quên `npm run build` trước demo | Trung bình | Tất cả | Đưa vào checklist DoD và checklist trước demo |
| Mất dữ liệu khi demo | Trung bình | S6 | Seeder demo + backup DB trước buổi bảo vệ |
| Hết thời gian do bug UAT | Cao | S6 | Dành 10 h buffer S6-12; chỉ sửa bug chặn demo |

### 8.2. Giả định

- Nhóm **1–2 người**, mỗi ngày 6 h, 10 ngày/2 tuần; các "vai" Backend/Frontend/Tester/DevOps là **vai trò**, một người có thể kiêm nhiệm.
- **ADR-04 đã được xác nhận** trước Sprint 4: trọng số QT 40% – Thi 60%, thang điểm chữ/hệ 4 theo mục 3.2 của `SPRINT_CHI_TIET.md`.
- Các quyết định kỹ thuật đang áp dụng: **Tailwind CSS 4** (không Bootstrap), auth tự viết bằng guard có sẵn, phân quyền bằng `users.role` + enum + middleware, test trên SQLite `:memory:`.
- Story Point là ước lượng **tương đối**; tổng 137 SP không phải tổng giờ. Giờ chi tiết lấy từ CSV.
- Tài liệu này **kế thừa cấu trúc** của `Vi-du-sprint.pdf`; các User Story về thi trắc nghiệm trong PDF **không** thuộc phạm vi đồ án QLSV.

### 8.3. Cách cập nhật tài liệu

1. Khi thay đổi phạm vi: cập nhật **mục 3 (Product Backlog)** → **mục 4 (Tổng quan sprint)** → đồng bộ `DANH_SACH_TASK_SPRINT.csv`.
2. Cuối mỗi sprint: ghi **SP cam kết / SP hoàn thành**, giờ thực tế, việc bị cắt vào `docs/04-nhat-ky-sprint.md`.
3. Nếu một US bị tách/nhập: giữ nguyên mã cũ trong ngoặc để truy vết (ví dụ `US-24a`), không tái sử dụng mã đã có.
4. Mọi thay đổi AC phải được cập nhật **trước khi** code task tương ứng bắt đầu.

---

*Hết tài liệu. Các file liên quan: `AGILE_QLSV.md` (kế hoạch cấp cao), `SPRINT_CHI_TIET.md` (task/AC kỹ thuật chi tiết), `DANH_SACH_TASK_SPRINT.csv` (nguồn task để import GitHub Projects/Trello).*
