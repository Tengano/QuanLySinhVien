# Tổng hợp dự án Quản lý Sinh viên

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

## 4. 5 nhóm chức năng 

| # | Nhóm chức năng | Làm được gì |
|---|---|---|
| 1 | **Đăng nhập & phân quyền** | Đăng nhập/đăng xuất 4 vai trò, khoá tài khoản, quên mật khẩu thì Admin reset; mỗi vai trò chỉ thấy menu/trang của mình |
| 2 | **Khoa – Ngành – Lớp** | Thêm/sửa/xoá, tìm kiếm; không cho xoá khi còn dữ liệu con |
| 3 | **Lớp học phần** | Mở lớp từ Môn + Học kỳ + Giảng viên, ghi danh sinh viên (chặn trùng, chặn vượt sĩ số) |
| 4 | **Điểm** | Nhập điểm quá trình/thi → tự tính điểm TB, điểm chữ, hệ 4, đạt/không đạt; chốt điểm để không sửa được nữa |
| 5 | **Thống kê** | Dashboard + biểu đồ (SV theo khoa/ngành, đạt–rớt, điểm TB theo kỳ); xuất Excel, in bảng điểm |

> Ngoài 5 nhóm trên còn có phần **hỗ trợ**: quản lý sinh viên (hồ sơ, ảnh, import CSV), thông báo nội bộ.

## 5. Dữ liệu chính (12 bảng — đã Việt hoá)

| Nhóm dữ liệu | Bảng |
|---|---|
| Tài khoản | `nguoi_dung` (phân biệt vai trò bằng cột `vai_tro`) |
| Danh mục | `khoa` (Khoa), `nganh` (Ngành), `lop` (Lớp), `mon_hoc` (Môn), `hoc_ky` (Học kỳ) |
| Sinh viên | `sinh_vien` |
| Lớp học phần | `lop_hoc_phan` (lớp HP), `ghi_danh` (ghi danh) |
| Điểm | `diem` (1 ghi danh ↔ 1 bộ điểm) |
| Thông báo | `thong_bao`, `thong_bao_nguoi_dung` (ai đã đọc) |

> 8 bảng hạ tầng của Laravel giữ nguyên tên gốc: `migrations`, `cache`, `cache_locks`,
> `jobs`, `job_batches`, `failed_jobs`, `sessions`, `password_reset_tokens`.
> Các cột Laravel bắt buộc cũng giữ nguyên: `id`, `email`, `password`, `remember_token`,
> `email_verified_at`, `created_at`, `updated_at`, `deleted_at`.

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

