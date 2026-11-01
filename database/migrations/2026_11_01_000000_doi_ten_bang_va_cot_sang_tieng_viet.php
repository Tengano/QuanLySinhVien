<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Việt hoá tên bảng + tên cột nghiệp vụ.
 *
 * - Giữ nguyên dữ liệu (chỉ RENAME, không drop/tạo lại bảng).
 * - Giữ nguyên 8 bảng hạ tầng của Laravel: migrations, cache, cache_locks,
 *   jobs, job_batches, failed_jobs, sessions, password_reset_tokens.
 * - Giữ nguyên các cột Laravel bắt buộc: id, email, password, remember_token,
 *   email_verified_at, created_at, updated_at, deleted_at.
 */
return new class extends Migration
{
    /**
     * Bảng: tên cũ => tên mới.
     *
     * @var array<string, string>
     */
    private array $tables = [
        'users' => 'nguoi_dung',
        'faculties' => 'khoa',
        'majors' => 'nganh',
        'classes' => 'lop',
        'subjects' => 'mon_hoc',
        'semesters' => 'hoc_ky',
        'students' => 'sinh_vien',
        'courses' => 'lop_hoc_phan',
        'enrollments' => 'ghi_danh',
        'grades' => 'diem',
        'announcements' => 'thong_bao',
        'announcement_user' => 'thong_bao_nguoi_dung',
    ];

    /**
     * Cột: bảng (tên mới) => [cột cũ => cột mới].
     *
     * @var array<string, array<string, string>>
     */
    private array $columns = [
        'nguoi_dung' => [
            'name' => 'ho_ten',
            'role' => 'vai_tro',
            'avatar' => 'anh_dai_dien',
            'last_login_at' => 'dang_nhap_cuoi_luc',
        ],
        'nganh' => [
            'faculty_id' => 'khoa_id',
        ],
        'lop' => [
            'major_id' => 'nganh_id',
            'gvcn_id' => 'giao_vien_chu_nhiem_id',
        ],
        'mon_hoc' => [
            'faculty_id' => 'khoa_id',
        ],
        'lop_hoc_phan' => [
            'subject_id' => 'mon_hoc_id',
            'semester_id' => 'hoc_ky_id',
        ],
        'sinh_vien' => [
            'user_id' => 'nguoi_dung_id',
        ],
        'ghi_danh' => [
            'student_id' => 'sinh_vien_id',
            'course_id' => 'lop_hoc_phan_id',
        ],
        'diem' => [
            'enrollment_id' => 'ghi_danh_id',
        ],
        'thong_bao' => [
            'lop_hp_id' => 'lop_hoc_phan_id',
        ],
        'thong_bao_nguoi_dung' => [
            'announcement_id' => 'thong_bao_id',
            'user_id' => 'nguoi_dung_id',
        ],
    ];

    /**
     * Khóa ngoại hiện có (bảng cũ => tên constraint).
     *
     * @var array<string, list<string>>
     */
    private array $oldForeignKeys = [
        'announcement_user' => ['announcement_user_announcement_id_foreign', 'announcement_user_user_id_foreign'],
        'announcements' => [
            'announcements_khoa_id_foreign',
            'announcements_lop_hp_id_foreign',
            'announcements_lop_id_foreign',
            'announcements_nguoi_gui_id_foreign',
            'announcements_nguoi_nhan_id_foreign',
        ],
        'classes' => ['classes_gvcn_id_foreign', 'classes_major_id_foreign'],
        'courses' => ['courses_giang_vien_id_foreign', 'courses_semester_id_foreign', 'courses_subject_id_foreign'],
        'enrollments' => ['enrollments_course_id_foreign', 'enrollments_student_id_foreign'],
        'grades' => ['grades_enrollment_id_foreign', 'grades_nguoi_nhap_id_foreign'],
        'majors' => ['majors_faculty_id_foreign'],
        'students' => ['students_lop_id_foreign', 'students_user_id_foreign'],
        'subjects' => ['subjects_faculty_id_foreign'],
    ];

    public function up(): void
    {
        foreach ($this->oldForeignKeys as $table => $constraints) {
            Schema::table($table, function (Blueprint $table) use ($constraints): void {
                foreach ($constraints as $constraint) {
                    $table->dropForeign($constraint);
                }
            });
        }

        foreach ($this->tables as $from => $to) {
            Schema::rename($from, $to);
        }

        foreach ($this->columns as $table => $columns) {
            Schema::table($table, function (Blueprint $blueprint) use ($columns): void {
                foreach ($columns as $from => $to) {
                    $blueprint->renameColumn($from, $to);
                }
            });
        }

        $this->addForeignKeys();
    }

    public function down(): void
    {
        foreach ($this->foreignKeys as [$table, $column]) {
            Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropForeign([$column]));
        }

        foreach ($this->columns as $table => $columns) {
            Schema::table($table, function (Blueprint $blueprint) use ($columns): void {
                foreach (array_reverse($columns, true) as $from => $to) {
                    $blueprint->renameColumn($to, $from);
                }
            });
        }

        foreach (array_reverse($this->tables, true) as $old => $new) {
            Schema::rename($new, $old);
        }

        foreach ($this->oldForeignKeys as $table => $constraints) {
            $columns = $this->oldForeignKeysColumns[$table] ?? [];

            Schema::table($table, function (Blueprint $blueprint) use ($constraints, $columns): void {
                foreach ($constraints as $index => $constraint) {
                    [$column, $referencedTable, $onDelete] = $columns[$index];

                    $foreign = $blueprint->foreign($column, $constraint)->references('id')->on($referencedTable);

                    match ($onDelete) {
                        'cascade' => $foreign->cascadeOnDelete(),
                        'null' => $foreign->nullOnDelete(),
                        default => $foreign->restrictOnDelete(),
                    };
                }
            });
        }
    }

    /**
     * Danh sách khóa ngoại của schema tiếng Việt: [bảng, cột, bảng tham chiếu, hành vi xoá].
     *
     * @var list<array{0: string, 1: string, 2: string, 3: string}>
     */
    private array $foreignKeys = [
        ['thong_bao_nguoi_dung', 'thong_bao_id', 'thong_bao', 'cascade'],
        ['thong_bao_nguoi_dung', 'nguoi_dung_id', 'nguoi_dung', 'cascade'],
        ['thong_bao', 'khoa_id', 'khoa', 'null'],
        ['thong_bao', 'lop_id', 'lop', 'null'],
        ['thong_bao', 'lop_hoc_phan_id', 'lop_hoc_phan', 'null'],
        ['thong_bao', 'nguoi_gui_id', 'nguoi_dung', 'null'],
        ['thong_bao', 'nguoi_nhan_id', 'nguoi_dung', 'null'],
        ['lop', 'giao_vien_chu_nhiem_id', 'nguoi_dung', 'null'],
        ['lop', 'nganh_id', 'nganh', 'restrict'],
        ['lop_hoc_phan', 'giang_vien_id', 'nguoi_dung', 'null'],
        ['lop_hoc_phan', 'hoc_ky_id', 'hoc_ky', 'restrict'],
        ['lop_hoc_phan', 'mon_hoc_id', 'mon_hoc', 'restrict'],
        ['ghi_danh', 'lop_hoc_phan_id', 'lop_hoc_phan', 'cascade'],
        ['ghi_danh', 'sinh_vien_id', 'sinh_vien', 'cascade'],
        ['diem', 'ghi_danh_id', 'ghi_danh', 'cascade'],
        ['diem', 'nguoi_nhap_id', 'nguoi_dung', 'null'],
        ['nganh', 'khoa_id', 'khoa', 'restrict'],
        ['sinh_vien', 'lop_id', 'lop', 'restrict'],
        ['sinh_vien', 'nguoi_dung_id', 'nguoi_dung', 'null'],
        ['mon_hoc', 'khoa_id', 'khoa', 'null'],
    ];

    /**
     * Cột + bảng tham chiếu của khóa ngoại CŨ (dùng khi rollback).
     *
     * @var array<string, list<array{0: string, 1: string, 2: string}>>
     */
    private array $oldForeignKeysColumns = [
        'announcement_user' => [['announcement_id', 'announcements', 'cascade'], ['user_id', 'users', 'cascade']],
        'announcements' => [
            ['khoa_id', 'faculties', 'null'],
            ['lop_hp_id', 'courses', 'null'],
            ['lop_id', 'classes', 'null'],
            ['nguoi_gui_id', 'users', 'null'],
            ['nguoi_nhan_id', 'users', 'null'],
        ],
        'classes' => [['gvcn_id', 'users', 'null'], ['major_id', 'majors', 'restrict']],
        'courses' => [
            ['giang_vien_id', 'users', 'null'],
            ['semester_id', 'semesters', 'restrict'],
            ['subject_id', 'subjects', 'restrict'],
        ],
        'enrollments' => [['course_id', 'courses', 'cascade'], ['student_id', 'students', 'cascade']],
        'grades' => [['enrollment_id', 'enrollments', 'cascade'], ['nguoi_nhap_id', 'users', 'null']],
        'majors' => [['faculty_id', 'faculties', 'restrict']],
        'students' => [['lop_id', 'classes', 'restrict'], ['user_id', 'users', 'null']],
        'subjects' => [['faculty_id', 'faculties', 'null']],
    ];

    private function addForeignKeys(): void
    {
        foreach ($this->foreignKeys as [$table, $column, $referencedTable, $onDelete]) {
            Schema::table($table, function (Blueprint $blueprint) use ($column, $referencedTable, $onDelete): void {
                $foreign = $blueprint->foreign($column)->references('id')->on($referencedTable);

                match ($onDelete) {
                    'cascade' => $foreign->cascadeOnDelete(),
                    'null' => $foreign->nullOnDelete(),
                    default => $foreign->restrictOnDelete(),
                };
            });
        }
    }
};
