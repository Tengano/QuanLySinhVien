@extends('layouts.app')

@section('title', 'Bảng điểm của tôi')
@section('subtitle', $student->ma_sv.' · '.$student->lop?->ten_lop)

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-xl font-semibold">{{ $student->ho_ten }}</p>
            <p class="text-sm text-slate-500">{{ $student->lop?->major?->ten_nganh }} · {{ $student->lop?->major?->faculty?->ten_khoa }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <form method="GET" class="flex items-center gap-2">
                <select name="semester_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Tất cả học kỳ</option>
                    @foreach ($semesters as $semester)
                        <option value="{{ $semester->id }}" @selected((string) $semesterId === (string) $semester->id)>{{ $semester->ten_hoc_ky }} · {{ $semester->nam_hoc }}</option>
                    @endforeach
                </select>
                <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Lọc</button>
            </form>
            <a href="{{ route('reports.transcript', $student) }}" target="_blank" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white">In / Lưu PDF</a>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs uppercase text-slate-500">GPA</p><p class="mt-2 text-3xl font-bold text-blue-700">{{ number_format($gpa['gpa'], 2) }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs uppercase text-slate-500">Tín chỉ đạt</p><p class="mt-2 text-3xl font-bold text-emerald-600">{{ $gpa['tin_chi_dat'] }}/{{ $gpa['tong_tin_chi'] }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs uppercase text-slate-500">Xếp loại</p><p class="mt-2 text-2xl font-bold">{{ $gpa['xep_loai'] }}</p></div>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4"><h2 class="font-semibold">Bảng điểm chi tiết</h2></div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr><th class="px-4 py-3">Học kỳ</th><th class="px-4 py-3">Môn học</th><th class="px-4 py-3">TC</th><th class="px-4 py-3">QT</th><th class="px-4 py-3">Thi</th><th class="px-4 py-3">TB</th><th class="px-4 py-3">Chữ</th><th class="px-4 py-3">Hệ 4</th><th class="px-4 py-3">Kết quả</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse ($enrollments as $enrollment)
                    <tr>
                        <td class="px-4 py-3">{{ $enrollment->course->semester?->ten_hoc_ky }} · {{ $enrollment->course->semester?->nam_hoc }}</td>
                        <td class="px-4 py-3 font-medium">{{ $enrollment->course->subject?->ten_mh }}</td>
                        <td class="px-4 py-3">{{ $enrollment->course->subject?->so_tin_chi }}</td>
                        <td class="px-4 py-3">{{ $enrollment->grade?->diem_qua_trinh ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $enrollment->grade?->diem_thi ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $enrollment->grade?->diem_tb ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $enrollment->grade?->diem_chu ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $enrollment->grade?->diem_he4 ?? '—' }}</td>
                        <td class="px-4 py-3">@if($enrollment->grade?->dat)<span class="text-emerald-600">Đạt</span>@else<span class="text-red-600">Không đạt</span>@endif</td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="px-4 py-10 text-center text-slate-500">Chưa có điểm đã chốt.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
