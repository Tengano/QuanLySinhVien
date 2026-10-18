<?php

namespace Database\Seeders;

use App\Enums\DoiTuongThongBao;
use App\Enums\GioiTinh;
use App\Enums\LoaiMonHoc;
use App\Enums\Role;
use App\Enums\TrangThaiDangKy;
use App\Enums\TrangThaiLopHocPhan;
use App\Enums\TrangThaiSinhVien;
use App\Models\Announcement;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Faculty;
use App\Models\Grade;
use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QlsvDemoSeeder extends Seeder
{
    public function run(): void
    {
        $users = $this->seedUsers();
        $danhMuc = $this->seedDanhMuc($users);
        $students = $this->seedStudents($danhMuc['classes'], $users);
        $courses = $this->seedCourses($danhMuc['subjects'], $danhMuc['semesters'], $users);

        $this->seedEnrollmentsAndGrades($courses, $students);
        $this->seedAnnouncements($users, $danhMuc['classes'], $courses);
    }

    /**
     * @return array<string, User>
     */
    private function seedUsers(): array
    {
        $rows = [
            'admin' => ['Quản trị viên', 'admin@qlsv.test', Role::ADMIN, 'Admin@123'],
            'giao_vu' => ['Nguyễn Thị Giáo Vụ', 'giaovu@qlsv.test', Role::GIAO_VU, 'Giaovu@123'],
            'gv_an' => ['TS. Nguyễn Văn An', 'an.nguyen@qlsv.test', Role::GIANG_VIEN, 'Giangvien@123'],
            'gv_binh' => ['ThS. Trần Thị Bình', 'binh.tran@qlsv.test', Role::GIANG_VIEN, 'Giangvien@123'],
            'sinhvien' => ['Nguyễn Văn An', 'sinhvien@qlsv.test', Role::SINH_VIEN, 'Sinhvien@123'],
        ];

        $users = [];

        foreach ($rows as $key => [$name, $email, $role, $password]) {
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'role' => $role,
                    'password' => $password,
                    'trang_thai' => true,
                    'email_verified_at' => now(),
                ]
            );

            // Đối soát vai trò cho tài khoản đã tồn tại (ví dụ sau khi rollback
            // chỉ cột role bị tạo lại với giá trị mặc định), không reset mật khẩu.
            $user->fill(['name' => $name, 'role' => $role, 'trang_thai' => true])->save();

            $users[$key] = $user;
        }

        return $users;
    }

    /**
     * @param  array<string, User>  $users
     * @return array<string, array<string, mixed>>
     */
    private function seedDanhMuc(array $users): array
    {
        $faculties = [
            'cntt' => Faculty::firstOrCreate(
                ['ma_khoa' => 'CNTT'],
                ['ten_khoa' => 'Công nghệ thông tin', 'truong_khoa' => 'TS. Nguyễn Văn An', 'email' => 'cntt@qlsv.test']
            ),
            'kt' => Faculty::firstOrCreate(
                ['ma_khoa' => 'KT'],
                ['ten_khoa' => 'Kinh tế', 'truong_khoa' => 'TS. Lê Thị Hoa', 'email' => 'kt@qlsv.test']
            ),
            'nn' => Faculty::firstOrCreate(
                ['ma_khoa' => 'NN'],
                ['ten_khoa' => 'Ngoại ngữ', 'truong_khoa' => 'ThS. Phạm Minh Anh', 'email' => 'nn@qlsv.test']
            ),
        ];

        $majorRows = [
            'ktpm' => ['cntt', 'KTPM', 'Kỹ thuật phần mềm'],
            'httt' => ['cntt', 'HTTT', 'Hệ thống thông tin'],
            'qtkd' => ['kt', 'QTKD', 'Quản trị kinh doanh'],
            'nnanh' => ['nn', 'NNANH', 'Ngôn ngữ Anh'],
        ];
        $majors = [];

        foreach ($majorRows as $key => [$facultyKey, $maNganh, $tenNganh]) {
            $majors[$key] = Major::firstOrCreate(
                ['ma_nganh' => $maNganh],
                ['faculty_id' => $faculties[$facultyKey]->id, 'ten_nganh' => $tenNganh, 'so_nam_dao_tao' => 4]
            );
        }

        $classRows = [
            'cntt_k23_a' => ['ktpm', 'CNTT-K23-A', 'Kỹ thuật phần mềm K23A', 2023, 'gv_an'],
            'cntt_k23_b' => ['ktpm', 'CNTT-K23-B', 'Kỹ thuật phần mềm K23B', 2023, 'gv_an'],
            'ktpm_k23_a' => ['ktpm', 'KTPM-K23-A', 'Kỹ thuật phần mềm K23A (CLC)', 2023, 'gv_binh'],
            'httt_k24_a' => ['httt', 'HTTT-K24-A', 'Hệ thống thông tin K24A', 2024, 'gv_binh'],
            'qtkd_k23_a' => ['qtkd', 'QTKD-K23-A', 'Quản trị kinh doanh K23A', 2023, 'gv_binh'],
            'nnanh_k24_a' => ['nnanh', 'NNANH-K24-A', 'Ngôn ngữ Anh K24A', 2024, 'gv_an'],
        ];
        $classes = [];

        foreach ($classRows as $key => [$majorKey, $maLop, $tenLop, $khoaHoc, $gvKey]) {
            $classes[$key] = SchoolClass::firstOrCreate(
                ['ma_lop' => $maLop],
                [
                    'major_id' => $majors[$majorKey]->id,
                    'ten_lop' => $tenLop,
                    'khoa_hoc' => $khoaHoc,
                    'gvcn_id' => $users[$gvKey]->id,
                ]
            );
        }

        $subjectRows = [
            'ltw' => ['cntt', 'LTW', 'Lập trình web', 3, LoaiMonHoc::BAT_BUOC, 30, 30],
            'csdl' => ['cntt', 'CSDL', 'Cơ sở dữ liệu', 3, LoaiMonHoc::BAT_BUOC, 30, 30],
            'oop' => ['cntt', 'OOP', 'Lập trình hướng đối tượng', 3, LoaiMonHoc::BAT_BUOC, 30, 30],
            'mmt' => ['cntt', 'MMT', 'Mạng máy tính', 3, LoaiMonHoc::BAT_BUOC, 30, 15],
            'tacb' => ['nn', 'TACB', 'Tiếng Anh cơ bản', 3, LoaiMonHoc::BAT_BUOC, 45, 0],
            'qtkd' => ['kt', 'QTKD', 'Quản trị kinh doanh', 3, LoaiMonHoc::TU_CHON, 45, 0],
            'ktct' => ['kt', 'KTCT', 'Kinh tế chính trị', 2, LoaiMonHoc::BAT_BUOC, 30, 0],
        ];
        $subjects = [];

        foreach ($subjectRows as $key => [$facultyKey, $maMh, $tenMh, $tinChi, $loai, $tietLt, $tietTh]) {
            $subjects[$key] = Subject::firstOrCreate(
                ['ma_mh' => $maMh],
                [
                    'faculty_id' => $faculties[$facultyKey]->id,
                    'ten_mh' => $tenMh,
                    'so_tin_chi' => $tinChi,
                    'loai_mh' => $loai,
                    'so_tiet_ly_thuyet' => $tietLt,
                    'so_tiet_thuc_hanh' => $tietTh,
                ]
            );
        }

        $semesters = [
            'hk1_2627' => Semester::firstOrCreate(
                ['nam_hoc' => '2026-2027', 'hoc_ky_so' => 1],
                ['ten_hoc_ky' => 'HK1', 'ngay_bat_dau' => '2026-09-01', 'ngay_ket_thuc' => '2027-01-15']
            ),
            'hk2_2627' => Semester::firstOrCreate(
                ['nam_hoc' => '2026-2027', 'hoc_ky_so' => 2],
                ['ten_hoc_ky' => 'HK2', 'ngay_bat_dau' => '2027-02-01', 'ngay_ket_thuc' => '2027-06-15']
            ),
            'hkhe_2627' => Semester::firstOrCreate(
                ['nam_hoc' => '2026-2027', 'hoc_ky_so' => 3],
                ['ten_hoc_ky' => 'HK Hè', 'ngay_bat_dau' => '2027-06-20', 'ngay_ket_thuc' => '2027-08-15']
            ),
        ];

        return compact('faculties', 'majors', 'classes', 'subjects', 'semesters');
    }

    /**
     * @param  array<string, SchoolClass>  $classes
     * @param  array<string, User>  $users
     * @return array<string, Student>
     */
    private function seedStudents(array $classes, array $users): array
    {
        $rows = [
            ['SV001', 'Nguyễn Văn An', '2005-03-12', GioiTinh::NAM, 'cntt_k23_a', 'an.nguyen05@qlsv.test', '0912000001'],
            ['SV002', 'Trần Thị Bích', '2005-07-08', GioiTinh::NU, 'cntt_k23_a', 'bich.tran@qlsv.test', '0912000002'],
            ['SV003', 'Lê Minh Cường', '2005-11-20', GioiTinh::NAM, 'cntt_k23_a', 'cuong.le@qlsv.test', '0912000003'],
            ['SV004', 'Phạm Thu Dung', '2005-01-15', GioiTinh::NU, 'cntt_k23_a', 'dung.pham@qlsv.test', '0912000004'],
            ['SV005', 'Hoàng Văn Đức', '2005-05-22', GioiTinh::NAM, 'cntt_k23_b', 'duc.hoang@qlsv.test', '0912000005'],
            ['SV006', 'Vũ Thị Hương', '2005-09-30', GioiTinh::NU, 'cntt_k23_b', 'huong.vu@qlsv.test', '0912000006'],
            ['SV007', 'Đặng Minh Khoa', '2005-12-05', GioiTinh::NAM, 'cntt_k23_b', 'khoa.dang@qlsv.test', '0912000007'],
            ['SV008', 'Bùi Thị Lan', '2005-04-18', GioiTinh::NU, 'ktpm_k23_a', 'lan.bui@qlsv.test', '0912000008'],
            ['SV009', 'Ngô Văn Minh', '2005-08-25', GioiTinh::NAM, 'ktpm_k23_a', 'minh.ngo@qlsv.test', '0912000009'],
            ['SV010', 'Dương Thị Ngọc', '2005-02-14', GioiTinh::NU, 'ktpm_k23_a', 'ngoc.duong@qlsv.test', '0912000010'],
            ['SV011', 'Lý Văn Phúc', '2004-06-11', GioiTinh::NAM, 'httt_k24_a', 'phuc.ly@qlsv.test', '0912000011'],
            ['SV012', 'Trịnh Thị Quỳnh', '2004-10-07', GioiTinh::NU, 'httt_k24_a', 'quynh.trinh@qlsv.test', '0912000012'],
            ['SV013', 'Cao Văn Sơn', '2005-03-03', GioiTinh::NAM, 'qtkd_k23_a', 'son.cao@qlsv.test', '0912000013'],
            ['SV014', 'Mai Thị Trang', '2005-07-19', GioiTinh::NU, 'qtkd_k23_a', 'trang.mai@qlsv.test', '0912000014'],
            ['SV015', 'Đỗ Minh Tuấn', '2004-12-01', GioiTinh::NAM, 'nnanh_k24_a', 'tuan.do@qlsv.test', '0912000015'],
        ];

        $students = [];

        foreach ($rows as [$maSv, $hoTen, $ngaySinh, $gioiTinh, $classKey, $email, $sdt]) {
            $students[$maSv] = Student::firstOrCreate(
                ['ma_sv' => $maSv],
                [
                    'ho_ten' => $hoTen,
                    'ngay_sinh' => $ngaySinh,
                    'gioi_tinh' => $gioiTinh,
                    'lop_id' => $classes[$classKey]->id,
                    'email' => $email,
                    'sdt' => $sdt,
                    'trang_thai' => TrangThaiSinhVien::DANG_HOC,
                    'ngay_nhap_hoc' => $classKey === 'httt_k24_a' || $classKey === 'nnanh_k24_a' ? '2024-09-05' : '2023-09-05',
                ]
            );
        }

        $students['SV001']->update(['user_id' => $users['sinhvien']->id]);

        return $students;
    }

    /**
     * @param  array<string, Subject>  $subjects
     * @param  array<string, Semester>  $semesters
     * @param  array<string, User>  $users
     * @return array<string, Course>
     */
    private function seedCourses(array $subjects, array $semesters, array $users): array
    {
        $rows = [
            'ltw' => ['LTW-HK1-2026-N01', 'ltw', 'gv_an', 'A2-301', 'Thứ 2, tiết 1-3', 50],
            'csdl' => ['CSDL-HK1-2026-N01', 'csdl', 'gv_binh', 'A2-302', 'Thứ 3, tiết 1-3', 50],
            'oop' => ['OOP-HK1-2026-N01', 'oop', 'gv_an', 'A2-303', 'Thứ 4, tiết 1-3', 50],
            'tacb' => ['TACB-HK1-2026-N01', 'tacb', 'gv_binh', 'B1-201', 'Thứ 5, tiết 1-3', 45],
            'qtkd' => ['QTKD-HK1-2026-N01', 'qtkd', 'gv_an', 'C1-101', 'Thứ 6, tiết 1-3', 60],
            'mmt' => ['MMT-HK1-2026-N01', 'mmt', 'gv_binh', 'A2-304', 'Thứ 7, tiết 1-3', 40],
        ];
        $courses = [];

        foreach ($rows as $key => [$maLhp, $subjectKey, $gvKey, $phong, $lich, $siSo]) {
            $courses[$key] = Course::firstOrCreate(
                ['ma_lhp' => $maLhp],
                [
                    'subject_id' => $subjects[$subjectKey]->id,
                    'semester_id' => $semesters['hk1_2627']->id,
                    'giang_vien_id' => $users[$gvKey]->id,
                    'phong_hoc' => $phong,
                    'lich_hoc' => $lich,
                    'so_sv_toi_da' => $siSo,
                    'trang_thai' => TrangThaiLopHocPhan::MO,
                ]
            );
        }

        return $courses;
    }

    /**
     * @param  array<string, Course>  $courses
     * @param  array<string, Student>  $students
     */
    private function seedEnrollmentsAndGrades(array $courses, array $students): void
    {
        $enrollmentMap = [
            'ltw' => ['SV001', 'SV002', 'SV003', 'SV004', 'SV005', 'SV006', 'SV007'],
            'csdl' => ['SV001', 'SV002', 'SV003', 'SV004', 'SV008', 'SV009', 'SV010'],
            'oop' => ['SV008', 'SV009', 'SV010'],
            'tacb' => ['SV001', 'SV005', 'SV008', 'SV011', 'SV013', 'SV015'],
            'qtkd' => ['SV013', 'SV014'],
        ];

        $diem = [
            'SV001' => [8.5, 9.0],
            'SV002' => [7.0, 8.0],
            'SV003' => [5.5, 6.0],
            'SV004' => [4.0, 3.5],
            'SV005' => [6.5, 7.0],
            'SV006' => [9.0, 8.5],
            'SV007' => [5.0, 5.5],
            'SV008' => [8.0, 7.5],
            'SV009' => [6.0, 6.5],
            'SV010' => [4.5, 5.0],
        ];

        foreach ($enrollmentMap as $courseKey => $maSvs) {
            foreach ($maSvs as $maSv) {
                $enrollment = Enrollment::firstOrCreate(
                    [
                        'student_id' => $students[$maSv]->id,
                        'course_id' => $courses[$courseKey]->id,
                        'lan_hoc' => 1,
                    ],
                    ['trang_thai' => TrangThaiDangKy::DANG_HOC]
                );

                if (! in_array($courseKey, ['ltw', 'csdl', 'oop'], true) || ! isset($diem[$maSv])) {
                    continue;
                }

                $ketQua = $this->tinhDiem($diem[$maSv][0], $diem[$maSv][1]);

                Grade::updateOrCreate(
                    ['enrollment_id' => $enrollment->id],
                    [
                        'diem_qua_trinh' => $diem[$maSv][0],
                        'diem_thi' => $diem[$maSv][1],
                        'diem_tb' => $ketQua['diem_tb'],
                        'diem_chu' => $ketQua['diem_chu'],
                        'diem_he4' => $ketQua['diem_he4'],
                        'dat' => $ketQua['dat'],
                        'nguoi_nhap_id' => $courses[$courseKey]->giang_vien_id,
                        'nhap_luc' => now(),
                        'khoa_luc' => $courseKey === 'ltw' ? now() : null,
                    ]
                );
            }
        }
    }

    /**
     * @param  array<string, User>  $users
     * @param  array<string, SchoolClass>  $classes
     * @param  array<string, Course>  $courses
     */
    private function seedAnnouncements(array $users, array $classes, array $courses): void
    {
        $toanTruong = Announcement::firstOrCreate(
            ['tieu_de' => 'Thông báo lịch thi học kỳ 1 năm học 2026-2027'],
            [
                'noi_dung' => 'Sinh viên theo dõi lịch thi và kiểm tra thông tin môn học trên cổng sinh viên.',
                'nguoi_gui_id' => $users['giao_vu']->id,
                'doi_tuong' => DoiTuongThongBao::TOAN_TRUONG,
            ]
        );
        $this->syncRecipients($toanTruong, User::query()->pluck('id')->all(), [$users['sinhvien']->id]);

        $theoLop = Announcement::firstOrCreate(
            ['tieu_de' => 'Sinh hoạt lớp CNTT-K23-A'],
            [
                'noi_dung' => 'Lớp CNTT-K23-A họp lớp vào 18h thứ Sáu tại phòng A2-301.',
                'nguoi_gui_id' => $users['giao_vu']->id,
                'doi_tuong' => DoiTuongThongBao::LOP,
                'lop_id' => $classes['cntt_k23_a']->id,
            ]
        );
        $userIds = $classes['cntt_k23_a']->students()->whereNotNull('user_id')->pluck('user_id')->all();
        $this->syncRecipients($theoLop, $userIds, [$users['sinhvien']->id]);

        $theoLopHp = Announcement::firstOrCreate(
            ['tieu_de' => 'Nhắc nộp bài tập Lập trình web'],
            [
                'noi_dung' => 'Hạn nộp bài tập tuần 8 là 23h59 ngày 25/10/2026 trên hệ thống.',
                'nguoi_gui_id' => $users['gv_an']->id,
                'doi_tuong' => DoiTuongThongBao::LOP_HOC_PHAN,
                'lop_hp_id' => $courses['ltw']->id,
            ]
        );
        $this->syncRecipients($theoLopHp, $userIds, [$users['sinhvien']->id]);
    }

    /**
     * @param  array<int, int>  $userIds
     * @param  array<int, int>  $readUserIds
     */
    private function syncRecipients(Announcement $announcement, array $userIds, array $readUserIds = []): void
    {
        foreach (array_unique($userIds) as $userId) {
            $daDoc = in_array($userId, $readUserIds, true);

            DB::table('announcement_user')->updateOrInsert(
                ['announcement_id' => $announcement->id, 'user_id' => $userId],
                [
                    'da_doc' => $daDoc,
                    'doc_luc' => $daDoc ? now() : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    /**
     * @return array{diem_tb: float, diem_chu: string, diem_he4: float, dat: bool}
     */
    private function tinhDiem(float $quaTrinh, float $thi): array
    {
        $tb = round(
            $quaTrinh * (float) config('diem.trong_so_qua_trinh')
            + $thi * (float) config('diem.trong_so_thi'),
            2
        );

        $quyDoi = ['chu' => 'F', 'he4' => 0.0];

        foreach (config('diem.quy_doi') as $row) {
            if ($tb >= (float) $row['tu']) {
                $quyDoi = $row;
                break;
            }
        }

        return [
            'diem_tb' => $tb,
            'diem_chu' => $quyDoi['chu'],
            'diem_he4' => (float) $quyDoi['he4'],
            'dat' => $tb >= (float) config('diem.diem_dat'),
        ];
    }
}
