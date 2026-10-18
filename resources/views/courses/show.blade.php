@extends('layouts.app')

@section('title', 'Chi tiết lớp học phần')
@section('subtitle', $course->ma_lhp.' · '.$course->subject?->ten_mh)

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('courses.index') }}" class="text-sm text-blue-700 hover:underline">← Danh sách lớp HP</a>
        <div class="flex flex-wrap gap-2">
            @if ($canEnroll)
                <a href="{{ route('courses.edit', $course) }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-600">Sửa lớp</a>
                <a href="{{ route('courses.enroll.create', $course) }}" class="rounded-lg border border-emerald-300 px-3 py-2 text-sm text-emerald-700">+ Ghi danh</a>
            @endif
            @if ($canGrade)
                <a href="{{ route('courses.grades.edit', $course) }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white">Nhập điểm</a>
            @endif
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs uppercase text-slate-500">Môn học</p><p class="mt-2 font-semibold">{{ $course->subject?->ten_mh }}</p><p class="text-xs text-slate-500">{{ $course->subject?->so_tin_chi }} tín chỉ</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs uppercase text-slate-500">Học kỳ</p><p class="mt-2 font-semibold">{{ $course->semester?->ten_hoc_ky }}</p><p class="text-xs text-slate-500">{{ $course->semester?->nam_hoc }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs uppercase text-slate-500">Giảng viên</p><p class="mt-2 font-semibold">{{ $course->giangVien?->name ?? 'Chưa phân công' }}</p><p class="text-xs text-slate-500">{{ $course->phong_hoc }} · {{ $course->lich_hoc }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs uppercase text-slate-500">Sĩ số / trạng thái</p><p class="mt-2 font-semibold">{{ $course->enrollments->count() }}/{{ $course->so_sv_toi_da }}</p><p class="text-xs text-slate-500">{{ $course->trang_thai->label() }}</p></div>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-4">
            <h2 class="font-semibold">Danh sách sinh viên</h2>
            <p class="text-sm text-slate-500">{{ $course->enrollments->count() }} sinh viên</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500"><tr><th class="px-4 py-3">Mã SV</th><th class="px-4 py-3">Họ tên</th><th class="px-4 py-3">Lớp</th><th class="px-4 py-3">QT</th><th class="px-4 py-3">Thi</th><th class="px-4 py-3">TB</th><th class="px-4 py-3">Chữ</th><th class="px-4 py-3">Trạng thái</th><th class="px-4 py-3"></th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                @forelse ($course->enrollments as $enrollment)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $enrollment->student?->ma_sv }}</td>
                        <td class="px-4 py-3">{{ $enrollment->student?->ho_ten }}</td>
                        <td class="px-4 py-3">{{ $enrollment->student?->lop?->ten_lop }}</td>
                        <td class="px-4 py-3">{{ $enrollment->grade?->diem_qua_trinh ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $enrollment->grade?->diem_thi ?? '—' }}</td>
                        <td class="px-4 py-3 font-semibold">{{ $enrollment->grade?->diem_tb ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $enrollment->grade?->diem_chu ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if ($enrollment->grade?->khoa_luc)
                                <span class="rounded-full bg-emerald-100 px-2 py-1 text-xs text-emerald-700">Đã chốt</span>
                            @else
                                <span class="rounded-full bg-slate-100 px-2 py-1 text-xs text-slate-600">{{ $enrollment->trang_thai->label() }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if ($canEnroll && ! $enrollment->grade?->khoa_luc)
                                <form method="POST" action="{{ route('enrollments.destroy', $enrollment) }}" onsubmit="return confirm('Rút sinh viên khỏi lớp?')">@csrf @method('DELETE')<button class="rounded-lg border border-red-200 px-2.5 py-1.5 text-xs text-red-600">Rút</button></form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="px-4 py-10 text-center text-slate-500">Chưa có sinh viên ghi danh.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
