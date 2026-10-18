<?php

namespace Tests\Feature;

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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(QlsvDemoSeeder::class);
    }

    private function user(string $email): User
    {
        return User::where('email', $email)->firstOrFail();
    }

    public function test_dang_nhap_thanh_cong_va_dang_xuat(): void
    {
        $this->post('/login', [
            'email' => 'admin@qlsv.test',
            'password' => 'Admin@123',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($this->user('admin@qlsv.test'));

        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_tai_khoan_bi_khoa_khong_the_dang_nhap(): void
    {
        $this->user('admin@qlsv.test')->update(['trang_thai' => false]);

        $this->post('/login', [
            'email' => 'admin@qlsv.test',
            'password' => 'Admin@123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_phan_quyen_route_theo_vai_tro(): void
    {
        $this->actingAs($this->user('admin@qlsv.test'))->get('/users')->assertOk()->assertSee('Danh sách tài khoản');
        $this->actingAs($this->user('sinhvien@qlsv.test'))->get('/users')->assertForbidden();
        $this->actingAs($this->user('giaovu@qlsv.test'))->get('/danh-muc/khoa')->assertOk();
        $this->actingAs($this->user('sinhvien@qlsv.test'))->get('/danh-muc/khoa')->assertForbidden();
    }

    public function test_admin_tao_khoa_moi_va_chan_xoa_khoa_con_du_lieu(): void
    {
        $admin = $this->user('admin@qlsv.test');

        $this->actingAs($admin)->post('/danh-muc/khoa', [
            'ma_khoa' => 'MOI',
            'ten_khoa' => 'Khoa mới',
        ])->assertRedirect('/danh-muc/khoa');

        $this->assertDatabaseHas('faculties', ['ma_khoa' => 'MOI']);

        $faculty = Faculty::where('ma_khoa', 'CNTT')->firstOrFail();

        $this->actingAs($admin)->delete('/danh-muc/khoa/'.$faculty->id)->assertSessionHas('error');
        $this->assertDatabaseHas('faculties', ['id' => $faculty->id]);
    }

    public function test_them_sinh_vien_va_tim_kiem(): void
    {
        $admin = $this->user('admin@qlsv.test');
        $class = SchoolClass::where('ma_lop', 'CNTT-K23-A')->firstOrFail();

        $this->actingAs($admin)->post('/sinh-vien', [
            'ma_sv' => 'SV099',
            'ho_ten' => 'Sinh Viên Kiểm Thử',
            'ngay_sinh' => '2005-05-05',
            'gioi_tinh' => 'nam',
            'lop_id' => $class->id,
            'email' => 'kiemthu@qlsv.test',
            'trang_thai' => 'dang_hoc',
        ])->assertRedirect('/sinh-vien');

        $this->assertDatabaseHas('students', ['ma_sv' => 'SV099']);

        $this->actingAs($admin)->get('/sinh-vien?q=SV099')
            ->assertOk()
            ->assertSee('Sinh Viên Kiểm Thử');
    }

    public function test_xoa_mem_va_khoi_phuc_sinh_vien(): void
    {
        $admin = $this->user('admin@qlsv.test');
        $student = Student::where('ma_sv', 'SV001')->firstOrFail();

        $this->actingAs($admin)->delete('/sinh-vien/'.$student->id)->assertRedirect('/sinh-vien');
        $this->assertSoftDeleted('students', ['id' => $student->id]);

        $this->actingAs($admin)->post('/sinh-vien/'.$student->id.'/restore')->assertRedirect('/sinh-vien/trash');
        $this->assertDatabaseHas('students', ['id' => $student->id, 'deleted_at' => null]);
    }

    public function test_giang_vien_nhap_diem_tinh_tu_dong_va_chot_diem(): void
    {
        $giangVien = $this->user('an.nguyen@qlsv.test');
        $course = Course::where('ma_lhp', 'OOP-HK1-2026-N01')->firstOrFail();
        $enrollment = Enrollment::where('course_id', $course->id)->firstOrFail();

        $this->actingAs($giangVien)->put('/lop-hoc-phan/'.$course->id.'/diem', [
            'grades' => [
                $enrollment->id => ['diem_qua_trinh' => 8, 'diem_thi' => 9],
            ],
        ])->assertSessionHas('success');

        $grade = Grade::where('enrollment_id', $enrollment->id)->firstOrFail();
        $this->assertSame('8.60', $grade->diem_tb);
        $this->assertSame('A', $grade->diem_chu);
        $this->assertTrue($grade->dat);

        $this->actingAs($giangVien)->post('/lop-hoc-phan/'.$course->id.'/diem/chot')->assertSessionHas('success');
        $this->assertNotNull($grade->fresh()->khoa_luc);

        $this->actingAs($giangVien)->put('/lop-hoc-phan/'.$course->id.'/diem', [
            'grades' => [
                $enrollment->id => ['diem_qua_trinh' => 1, 'diem_thi' => 1],
            ],
        ])->assertSessionHas('error');
    }

    public function test_giang_vien_khong_duoc_sua_diem_lop_nguoi_khac(): void
    {
        $giangVien = $this->user('an.nguyen@qlsv.test');
        $course = Course::where('ma_lhp', 'CSDL-HK1-2026-N01')->firstOrFail();

        $this->actingAs($giangVien)->get('/lop-hoc-phan/'.$course->id.'/diem')->assertForbidden();
    }

    public function test_sinh_vien_xem_bang_diem_gpa_va_in_bao_cao(): void
    {
        $sinhVien = $this->user('sinhvien@qlsv.test');
        $student = Student::where('ma_sv', 'SV001')->firstOrFail();

        $this->actingAs($sinhVien)->get('/sinh-vien/bang-diem')
            ->assertOk()
            ->assertSee('Bảng điểm chi tiết')
            ->assertSee('Lập trình web');

        $this->actingAs($sinhVien)->get('/bao-cao/bang-diem/'.$student->id)
            ->assertOk()
            ->assertSee('BẢNG ĐIỂM SINH VIÊN');

        $other = Student::where('ma_sv', 'SV002')->firstOrFail();
        $this->actingAs($sinhVien)->get('/bao-cao/bang-diem/'.$other->id)->assertForbidden();
    }

    public function test_dashboard_hoat_dong_voi_ba_vai_tro(): void
    {
        $this->actingAs($this->user('admin@qlsv.test'))->get('/dashboard')->assertOk()->assertSee('Sinh viên đang học');
        $this->actingAs($this->user('an.nguyen@qlsv.test'))->get('/dashboard')->assertOk()->assertSee('Lớp học phần của tôi');
        $this->actingAs($this->user('sinhvien@qlsv.test'))->get('/dashboard')->assertOk()->assertSee('GPA tích luỹ');
    }

    public function test_import_export_csv_va_doi_mat_khau(): void
    {
        $admin = $this->user('admin@qlsv.test');

        $csv = "ma_sv,ho_ten,ngay_sinh,gioi_tinh,ma_lop,email,sdt,cccd,dia_chi\n"
            ."SV100,Nguyễn Văn Import,2005-02-02,nam,CNTT-K23-A,import@qlsv.test,0909000001,,Hà Nội\n";

        $this->actingAs($admin)->post('/sinh-vien/import', [
            'file' => UploadedFile::fake()->createWithContent('students.csv', $csv),
        ])->assertRedirect('/sinh-vien')->assertSessionHas('success');

        $this->assertDatabaseHas('students', ['ma_sv' => 'SV100']);
        $this->actingAs($admin)->get('/sinh-vien/template')->assertOk();
        $this->actingAs($admin)->get('/sinh-vien/export')->assertOk();

        $this->actingAs($admin)->put('/profile/password', [
            'current_password' => 'Admin@123',
            'password' => 'NewPass@123',
            'password_confirmation' => 'NewPass@123',
        ])->assertSessionHas('success');

        $this->assertTrue(Hash::check('NewPass@123', $admin->fresh()->password));
    }

    public function test_gui_thong_bao_theo_lop_va_sinh_vien_doc_thong_bao(): void
    {
        $giaoVu = $this->user('giaovu@qlsv.test');
        $sinhVien = $this->user('sinhvien@qlsv.test');
        $class = SchoolClass::where('ma_lop', 'CNTT-K23-A')->firstOrFail();

        $this->actingAs($giaoVu)->post('/thong-bao', [
            'tieu_de' => 'Thông báo kiểm thử',
            'noi_dung' => 'Nội dung kiểm thử gửi theo lớp.',
            'doi_tuong' => 'lop',
            'lop_id' => $class->id,
        ])->assertRedirect('/thong-bao');

        $announcement = Announcement::where('tieu_de', 'Thông báo kiểm thử')->firstOrFail();

        $this->assertDatabaseHas('announcement_user', [
            'announcement_id' => $announcement->id,
            'user_id' => $sinhVien->id,
            'da_doc' => false,
        ]);

        $this->actingAs($sinhVien)->get('/hop-thu')->assertOk()->assertSee('Thông báo kiểm thử');
        $this->actingAs($sinhVien)->get('/hop-thu/'.$announcement->id)->assertOk()->assertSee('Nội dung kiểm thử');

        $this->assertDatabaseHas('announcement_user', [
            'announcement_id' => $announcement->id,
            'user_id' => $sinhVien->id,
            'da_doc' => true,
        ]);
    }

    public function test_mo_duoc_cac_trang_quan_tri_chinh(): void
    {
        $admin = $this->user('admin@qlsv.test');
        $faculty = Faculty::firstOrFail();
        $major = Major::firstOrFail();
        $class = SchoolClass::firstOrFail();
        $subject = Subject::firstOrFail();
        $semester = Semester::firstOrFail();
        $student = Student::firstOrFail();
        $course = Course::firstOrFail();
        $user = $this->user('an.nguyen@qlsv.test');
        $announcement = Announcement::firstOrFail();

        $this->actingAs($admin);

        $urls = [
            '/dashboard', '/profile', '/users', '/users/create', '/users/'.$user->id.'/edit',
            '/danh-muc/khoa', '/danh-muc/khoa/create', '/danh-muc/khoa/'.$faculty->id.'/edit',
            '/danh-muc/nganh', '/danh-muc/nganh/create', '/danh-muc/nganh/'.$major->id.'/edit',
            '/danh-muc/lop', '/danh-muc/lop/create', '/danh-muc/lop/'.$class->id.'/edit',
            '/danh-muc/mon-hoc', '/danh-muc/mon-hoc/create', '/danh-muc/mon-hoc/'.$subject->id.'/edit',
            '/danh-muc/hoc-ky', '/danh-muc/hoc-ky/create', '/danh-muc/hoc-ky/'.$semester->id.'/edit',
            '/sinh-vien', '/sinh-vien/create', '/sinh-vien/'.$student->id, '/sinh-vien/'.$student->id.'/edit',
            '/sinh-vien/trash', '/sinh-vien/import',
            '/lop-hoc-phan', '/lop-hoc-phan/create', '/lop-hoc-phan/'.$course->id,
            '/lop-hoc-phan/'.$course->id.'/edit', '/lop-hoc-phan/'.$course->id.'/diem',
            '/lop-hoc-phan/'.$course->id.'/ghi-danh', '/bao-cao/lop-hoc-phan/'.$course->id,
            '/thong-bao', '/thong-bao/create', '/hop-thu',
        ];

        foreach ($urls as $url) {
            $this->get($url)->assertOk();
        }

        $this->get('/hop-thu/'.$announcement->id)->assertOk();
    }
}
