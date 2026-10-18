<?php

namespace App\Services;

use App\Enums\Role;
use App\Enums\TrangThaiLopHocPhan;
use App\Enums\TrangThaiSinhVien;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class ThongKeService
{
    /**
     * @return array<string, mixed>
     */
    public function admin(): array
    {
        return Cache::remember('dashboard.admin', now()->addMinutes(5), function (): array {
            $cards = [
                'sinh_vien' => Student::query()->where('trang_thai', TrangThaiSinhVien::DANG_HOC->value)->count(),
                'lop_hp' => Course::query()->where('trang_thai', TrangThaiLopHocPhan::MO->value)->count(),
                'mon_hoc' => Subject::query()->count(),
                'giang_vien' => User::query()->where('role', Role::GIANG_VIEN->value)->where('trang_thai', true)->count(),
            ];

            $svTheoKhoa = DB::table('students')
                ->join('classes', 'students.lop_id', '=', 'classes.id')
                ->join('majors', 'classes.major_id', '=', 'majors.id')
                ->join('faculties', 'majors.faculty_id', '=', 'faculties.id')
                ->whereNull('students.deleted_at')
                ->where('students.trang_thai', TrangThaiSinhVien::DANG_HOC->value)
                ->select('faculties.ten_khoa', DB::raw('COUNT(*) AS total'))
                ->groupBy('faculties.ten_khoa')
                ->orderByDesc('total')
                ->pluck('total', 'ten_khoa')
                ->all();

            $svTheoNganh = DB::table('students')
                ->join('classes', 'students.lop_id', '=', 'classes.id')
                ->join('majors', 'classes.major_id', '=', 'majors.id')
                ->whereNull('students.deleted_at')
                ->where('students.trang_thai', TrangThaiSinhVien::DANG_HOC->value)
                ->select('majors.ten_nganh', DB::raw('COUNT(*) AS total'))
                ->groupBy('majors.ten_nganh')
                ->orderByDesc('total')
                ->limit(6)
                ->pluck('total', 'ten_nganh')
                ->all();

            $datRot = DB::table('grades')
                ->join('enrollments', 'grades.enrollment_id', '=', 'enrollments.id')
                ->join('courses', 'enrollments.course_id', '=', 'courses.id')
                ->join('subjects', 'courses.subject_id', '=', 'subjects.id')
                ->whereNotNull('grades.dat')
                ->selectRaw('subjects.ten_mh, SUM(CASE WHEN grades.dat = 1 THEN 1 ELSE 0 END) AS dat, SUM(CASE WHEN grades.dat = 0 THEN 1 ELSE 0 END) AS rot')
                ->groupBy('subjects.ten_mh')
                ->orderByRaw('COUNT(*) DESC')
                ->limit(8)
                ->get();

            $diemTheoKy = DB::table('grades')
                ->join('enrollments', 'grades.enrollment_id', '=', 'enrollments.id')
                ->join('courses', 'enrollments.course_id', '=', 'courses.id')
                ->join('semesters', 'courses.semester_id', '=', 'semesters.id')
                ->whereNotNull('grades.diem_tb')
                ->select('semesters.nam_hoc', 'semesters.hoc_ky_so', 'semesters.ten_hoc_ky', DB::raw('AVG(grades.diem_tb) AS trung_binh'))
                ->groupBy('semesters.id', 'semesters.nam_hoc', 'semesters.hoc_ky_so', 'semesters.ten_hoc_ky')
                ->orderBy('semesters.nam_hoc')
                ->orderBy('semesters.hoc_ky_so')
                ->get();

            return compact('cards', 'svTheoKhoa', 'svTheoNganh', 'datRot', 'diemTheoKy');
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function giangVien(User $user): array
    {
        $courses = Course::query()
            ->with(['subject', 'semester'])
            ->withCount('enrollments')
            ->where('giang_vien_id', $user->id)
            ->orderByDesc('id')
            ->get();

        $chuaNhap = $courses->filter(function (Course $course): bool {
            return ! Grade::query()
                ->whereHas('enrollment', fn ($query) => $query->where('course_id', $course->id))
                ->exists();
        })->count();

        return compact('courses', 'chuaNhap');
    }
}
