## 1. Bảng tổng quan

| Sprint | Hạng mục | Nội dung chính | Kết quả demo |
|:--|:--:|---|---|
| **SP1**<br>tuần 1–2 | (1) | Đăng nhập/đăng xuất cho **Admin, Giáo vụ, Giảng viên, Sinh viên** · phân quyền menu & route · quản lý tài khoản (tạo, khoá, reset mật khẩu) · hồ sơ + đổi mật khẩu | 4 vai trò vào đúng màn hình của mình; sinh viên mở `/users` → **403** |
| **SP2**<br>tuần 3–4 | (2) + (3) | Khoa → Ngành → Lớp (chặn xoá khi còn dữ liệu con) · Môn học & Học kỳ · Sinh viên (CRUD, import CSV) · Lớp học phần + phân công giảng viên + ghi danh | Tạo Khoa → Ngành → Lớp → Môn → Học kỳ, mở lớp học phần và ghi danh sinh viên |
| **SP3**<br>tuần 5–6 | (4) | Nhập điểm quá trình/thi · tự tính **TB – điểm chữ – hệ 4 – đạt** · chốt điểm & mở khoá · sinh viên xem bảng điểm + GPA | Nhập 8/9 → TB **8,60 (A)**; chốt điểm rồi không sửa được; SV xem điểm của mình |
| **SP4**<br>tuần 7–8 | (5) | Dashboard theo vai trò + biểu đồ (SV theo khoa/ngành, đạt–rớt, điểm TB theo kỳ) · xuất CSV · in bảng điểm | Dashboard có số liệu; xuất được Excel và in bảng điểm |

## 3. Thứ tự thực hiện trong mỗi sprint

```
SP1: Layout + đăng nhập  →  phân quyền  →  quản lý tài khoản  →  hồ sơ
SP2: Khoa/Ngành/Lớp  →  Môn học/Học kỳ  →  Sinh viên  →  Lớp HP  →  Ghi danh
SP3: Nhập điểm  →  công thức tính điểm  →  chốt điểm  →  cổng sinh viên
SP4: Dashboard  →  biểu đồ  →  export CSV  →  in bảng điểm  →  UAT
```
