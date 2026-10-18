@extends('layouts.app')

@section('title', 'Hồ sơ sinh viên')
@section('subtitle', $student->ma_sv.' · '.$student->lop?->ten_lop)

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('students.index') }}" class="text-sm text-blue-700 hover:underline">← Danh sách sinh viên</a>
        <div class="flex gap-2">
            <a href="{{ route('students.edit', $student) }}" class="rounded-lg border border-blue-200 px-3 py-2 text-sm text-blue-700">Sửa hồ sơ</a>
            <a href="{{ route('reports.transcript', $student) }}" target="_blank" class="rounded-lg bg-slate-900 px-3 py-2 text-sm font-medium text-white">In / Lưu PDF</a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm">
            @if ($student->anh)
                <img src="{{ asset('storage/'.$student->anh) }}" alt="" class="mx-auto h-28 w-28 rounded-full object-cover">
            @else
                <div class="mx-auto flex h-28 w-28 items-center justify-center rounded-full bg-blue-100 text-4xl font-bold text-blue-700">{{ mb_substr($student->ho_ten, 0, 1) }}</div>
            @endif
            <h2 class="mt-4 text-lg font-semibold">{{ $student->ho_ten }}</h2>
            <p class="text-sm text-slate-500">{{ $student->ma_sv }}</p>
            <span class="mt-3 inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs text-slate-600">{{ $student->trang_thai->label() }}</span>

            <dl class="mt-6 space-y-2 text-left text-sm">
                <div class="flex justify-between gap-3"><dt class="text-slate-500">Lớp</dt><dd class="font-medium">{{ $student->lop?->ten_lop }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-500">Ngành</dt><dd class="font-medium">{{ $student->lop?->major?->ten_nganh }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-500">Khoa</dt><dd class="font-medium">{{ $student->lop?->major?->faculty?->ten_khoa }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-500">Ngày sinh</dt><dd>{{ $student->ngay_sinh?->format('d/m/Y') }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-500">Giới tính</dt><dd>{{ $student->gioi_tinh->label() }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-500">Email</dt><dd>{{ $student->email ?? '—' }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-500">SĐT</dt><dd>{{ $student->sdt ?? '—' }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-500">Tài khoản</dt><dd>{{ $student->user?->email ?? 'Chưa cấp' }}</dd></div>
            </dl>
        </div>

        <div class="space-y-6 lg:col-span-2">
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs uppercase text-slate-500">GPA tích luỹ</p><p class="mt-2 text-3xl font-bold text-blue-700">{{ number_format($gpa['gpa'], 2) }}</p></div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs uppercase text-slate-500">Tín chỉ đạt</p><p class="mt-2 text-3xl font-bold text-emerald-600">{{ $gpa['tin_chi_dat'] }}/{{ $gpa['tong_tin_chi'] }}</p></div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs uppercase text-slate-500">Xếp loại</p><p class="mt-2 text-2xl font-bold">{{ $gpa['xep_loai'] }}</p></div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4"><h3 class="font-semibold">Kết quả học tập</h3></div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500"><tr><th class="px-4 py-3">Học kỳ</th><th class="px-4 py-3">Môn học</th><th class="px-4 py-3">TC</th><th class="px-4 py-3">QT</th><th class="px-4 py-3">Thi</th><th class="px-4 py-3">TB</th><th class="px-4 py-3">Chữ</th><th class="px-4 py-3">Hệ 4</th></tr></thead>
                        <tbody class="divide-y divide-slate-100">
                        @forelse ($student->enrollments as $enrollment)
                            <tr>
                                <td class="px-4 py-3">{{ $enrollment->course->semester->ten_hoc_ky }} · {{ $enrollment->course->semester->nam_hoc }}</td>
                                <td class="px-4 py-3 font-medium">{{ $enrollment->course->subject->ten_mh }}</td>
                                <td class="px-4 py-3">{{ $enrollment->course->subject->so_tin_chi }}</td>
                                <td class="px-4 py-3">{{ $enrollment->grade?->diem_qua_trinh ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $enrollment->grade?->diem_thi ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $enrollment->grade?->diem_tb ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $enrollment->grade?->diem_chu ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $enrollment->grade?->diem_he4 ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-4 py-8 text-center text-slate-500">Chưa có lớp học phần.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
