@extends('layouts.app')

@section('title', 'Nhập điểm')
@section('subtitle', $course->ma_lhp.' · '.$course->subject?->ten_mh)

@php($locked = $course->enrollments->contains(fn ($enrollment) => $enrollment->grade?->khoa_luc))

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div>
            <a href="{{ route('courses.show', $course) }}" class="text-sm text-blue-700 hover:underline">← Chi tiết lớp HP</a>
            <p class="mt-1 text-sm text-slate-500">
                {{ $course->semester?->ten_hoc_ky }} · {{ $course->semester?->nam_hoc }} · {{ $course->enrollments->count() }} sinh viên
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if (Route::has('reports.grades'))
                <a href="{{ route('reports.grades', $course) }}" target="_blank" class="rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-600">In / PDF</a>
            @endif
            @if ($locked)
                <span class="rounded-lg bg-emerald-100 px-3 py-2 text-sm font-medium text-emerald-700">Đã chốt điểm</span>
                @if (in_array(auth()->user()->role, [\App\Enums\Role::ADMIN, \App\Enums\Role::GIAO_VU], true))
                    <form method="POST" action="{{ route('courses.grades.unlock', $course) }}" onsubmit="return confirm('Mở khoá để sửa điểm?')">@csrf<button class="rounded-lg border border-amber-300 px-3 py-2 text-sm text-amber-700">Mở khoá</button></form>
                @endif
            @else
                <form method="POST" action="{{ route('courses.grades.lock', $course) }}" onsubmit="return confirm('Chốt điểm toàn lớp? Sau khi chốt sẽ không sửa được.')">@csrf<button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Chốt điểm</button></form>
            @endif
        </div>
    </div>

    <form method="POST" action="{{ route('courses.grades.update', $course) }}">
        @csrf
        @method('PUT')

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                    <tr><th class="px-4 py-3">Mã SV</th><th class="px-4 py-3">Họ tên</th><th class="px-4 py-3">Lớp</th><th class="px-4 py-3">Điểm QT</th><th class="px-4 py-3">Điểm thi</th><th class="px-4 py-3">TB</th><th class="px-4 py-3">Chữ</th><th class="px-4 py-3">Hệ 4</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    @forelse ($course->enrollments as $enrollment)
                        <tr>
                            <td class="px-4 py-2 font-medium">{{ $enrollment->student?->ma_sv }}</td>
                            <td class="px-4 py-2">{{ $enrollment->student?->ho_ten }}</td>
                            <td class="px-4 py-2">{{ $enrollment->student?->lop?->ten_lop }}</td>
                            <td class="px-4 py-2">
                                <input type="number" step="0.01" min="0" max="10"
                                       name="grades[{{ $enrollment->id }}][diem_qua_trinh]"
                                       value="{{ old("grades.{$enrollment->id}.diem_qua_trinh", $enrollment->grade?->diem_qua_trinh) }}"
                                       @disabled($locked)
                                       class="w-24 rounded-lg border border-slate-300 px-2 py-1.5 text-sm disabled:bg-slate-100">
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" step="0.01" min="0" max="10"
                                       name="grades[{{ $enrollment->id }}][diem_thi]"
                                       value="{{ old("grades.{$enrollment->id}.diem_thi", $enrollment->grade?->diem_thi) }}"
                                       @disabled($locked)
                                       class="w-24 rounded-lg border border-slate-300 px-2 py-1.5 text-sm disabled:bg-slate-100">
                            </td>
                            <td class="px-4 py-2 font-semibold">{{ $enrollment->grade?->diem_tb ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $enrollment->grade?->diem_chu ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $enrollment->grade?->diem_he4 ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-10 text-center text-slate-500">Chưa có sinh viên ghi danh.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @unless ($locked)
            <div class="mt-4 flex justify-end">
                <button class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Lưu điểm & tính lại</button>
            </div>
        @endunless
    </form>
@endsection
