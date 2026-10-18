<?php

namespace Tests\Feature;

use App\Enums\DoiTuongThongBao;
use App\Enums\GioiTinh;
use App\Enums\Role;
use App\Enums\TrangThaiLopHocPhan;
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
use Database\Seeders\QlsvDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_cac_bang_nghiep_vu_duoc_tao_day_du(): void
    {
        $tables = [
            'users',
            'faculties',
            'majors',
            'classes',
            'subjects',
            'semesters',
            'students',
            'courses',
            'enrollments',
            'grades',
            'announcements',
            'announcement_user',
        ];

        foreach ($tables as $table) {
            $this->assertTrue(Schema::hasTable($table), "Thiếu bảng {$table}");
        }
    }

    public function test_quan_he_va_enum_hoat_dong(): void
    {
        $giangVien = User::factory()->create(['role' => Role::GIANG_VIEN]);

        $faculty = Faculty::create([
            'ma_khoa' => 'CNTT',
            'ten_khoa' => 'Công nghệ thông tin',
        ]);

        $major = Major::create([
            'faculty_id' => $faculty->id,
            'ma_nganh' => 'KTPM',
            'ten_nganh' => 'Kỹ thuật phần mềm',
        ]);

        $class = SchoolClass::create([
            'major_id' => $major->id,
            'ma_lop' => 'CNTT-K23-A',
            'ten_lop' => 'Kỹ thuật phần mềm K23A',
            'khoa_hoc' => 2023,
            'gvcn_id' => $giangVien->id,
        ]);

        $subject = Subject::create([
            'faculty_id' => $faculty->id,
            'ma_mh' => 'LTW',
            'ten_mh' => 'Lập trình web',
            'so_tin_chi' => 3,
        ]);

        $semester = Semester::create([
            'ten_hoc_ky' => 'HK1',
            'hoc_ky_so' => 1,
            'nam_hoc' => '2026-2027',
            'ngay_bat_dau' => '2026-09-01',
            'ngay_ket_thuc' => '2027-01-15',
        ]);

        $student = Student::create([
            'ma_sv' => 'SV001',
            'ho_ten' => 'Nguyễn Văn An',
            'ngay_sinh' => '2005-03-12',
            'gioi_tinh' => GioiTinh::NAM,
            'lop_id' => $class->id,
        ]);

        $course = Course::create([
            'ma_lhp' => 'LTW-HK1-2026-N01',
            'subject_id' => $subject->id,
            'semester_id' => $semester->id,
            'giang_vien_id' => $giangVien->id,
            'trang_thai' => TrangThaiLopHocPhan::MO,
        ]);

        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
        ]);

        $grade = Grade::create([
            'enrollment_id' => $enrollment->id,
            'diem_qua_trinh' => 8.5,
            'diem_thi' => 9.0,
            'diem_tb' => 8.8,
            'diem_chu' => 'A',
            'diem_he4' => 4.0,
            'dat' => true,
        ]);

        $this->assertTrue($student->lop->is($class));
        $this->assertTrue($class->major->is($major));
        $this->assertTrue($major->faculty->is($faculty));
        $this->assertTrue($course->subject->is($subject));
        $this->assertTrue($enrollment->grade->is($grade));
        $this->assertTrue($course->students->contains($student));
        $this->assertSame(GioiTinh::NAM, $student->gioi_tinh);
        $this->assertSame(TrangThaiLopHocPhan::MO, $course->trang_thai);
    }

    public function test_bang_thong_bao_pivot_hoat_dong(): void
    {
        $user = User::factory()->create();
        $announcement = Announcement::create([
            'tieu_de' => 'Thông báo lịch thi',
            'noi_dung' => 'Nội dung thông báo',
            'doi_tuong' => DoiTuongThongBao::TOAN_TRUONG,
        ]);

        $announcement->users()->attach($user->id, ['da_doc' => false]);

        $this->assertTrue($user->announcements->contains($announcement));
        $this->assertFalse((bool) $user->announcements->first()->pivot->da_doc);
    }

    public function test_seeder_demo_tao_du_lieu_lien_ket(): void
    {
        $this->seed(QlsvDemoSeeder::class);

        $this->assertDatabaseCount('users', 5);
        $this->assertDatabaseCount('faculties', 3);
        $this->assertDatabaseCount('students', 15);
        $this->assertDatabaseCount('courses', 6);
        $this->assertDatabaseCount('enrollments', 25);
        $this->assertDatabaseCount('grades', 17);
        $this->assertDatabaseHas('users', ['email' => 'admin@qlsv.test', 'role' => 'admin']);
        $this->assertDatabaseHas('users', ['email' => 'giaovu@qlsv.test', 'role' => 'giao_vu']);

        $student = Student::with('lop.major.faculty', 'user')
            ->where('ma_sv', 'SV001')
            ->firstOrFail();

        $this->assertSame('Kỹ thuật phần mềm', $student->lop->major->ten_nganh);
        $this->assertSame('Công nghệ thông tin', $student->lop->major->faculty->ten_khoa);
        $this->assertSame('sinhvien@qlsv.test', $student->user?->email);
        $this->assertSame(GioiTinh::NAM, $student->gioi_tinh);
    }
}
