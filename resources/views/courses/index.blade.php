@extends('layouts.app')

@section('title', 'Lớp học phần')
@section('subtitle', 'Mở lớp, phân công giảng viên và quản lý điểm')

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold">Danh sách lớp học phần</h2>
            <p class="text-sm text-slate-500">{{ $courses->total() }} lớp</p>
        </div>
        @if ($canCreate)
            <a href="{{ route('courses.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">+ Mở lớp học phần</a>
        @endif
    </div>

    <form method="GET" class="mb-4 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-5">
        <div class="lg:col-span-2">
            <label class="mb-1 block text-xs font-medium text-slate-500">Tìm kiếm</label>
            <input name="q" value="{{ request('q') }}" placeholder="Mã lớp HP hoặc tên môn..." class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-500">Học kỳ</label>
            <select name="semester_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">Tất cả</option>
                @foreach ($semesters as $semester)
                    <option value="{{ $semester->id }}" @selected((string) request('semester_id') === (string) $semester->id)>{{ $semester->ten_hoc_ky }} · {{ $semester->nam_hoc }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-500">Môn học</label>
            <select name="subject_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">Tất cả</option>
                @foreach ($subjects as $id => $name)<option value="{{ $id }}" @selected((string) request('subject_id') === (string) $id)>{{ $name }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-500">Trạng thái</label>
            <select name="trang_thai" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">Tất cả</option>
                @foreach ($statuses as $value => $label)<option value="{{ $value }}" @selected(request('trang_thai') === $value)>{{ $label }}</option>@endforeach
            </select>
        </div>
        <div class="flex items-end gap-2 lg:col-span-5">
            <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Lọc</button>
            <a href="{{ route('courses.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600">Xoá lọc</a>
        </div>
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr><th class="px-4 py-3">Mã lớp HP</th><th class="px-4 py-3">Môn học</th><th class="px-4 py-3">Học kỳ</th><th class="px-4 py-3">Giảng viên</th><th class="px-4 py-3">Sĩ số</th><th class="px-4 py-3">Trạng thái</th><th class="px-4 py-3 text-right">Thao tác</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse ($courses as $course)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">{{ $course->ma_lhp }}</td>
                        <td class="px-4 py-3">{{ $course->subject?->ten_mh }}</td>
                        <td class="px-4 py-3">{{ $course->semester?->ten_hoc_ky }} · {{ $course->semester?->nam_hoc }}</td>
                        <td class="px-4 py-3">{{ $course->giangVien?->name ?? 'Chưa phân công' }}</td>
                        <td class="px-4 py-3">{{ $course->enrollments_count }}/{{ $course->so_sv_toi_da }}</td>
                        <td class="px-4 py-3">{{ $course->trang_thai->label() }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-1">
                                <a href="{{ route('courses.show', $course) }}" class="rounded-lg border border-slate-300 px-2.5 py-1.5 text-xs text-slate-600">Xem</a>
                                <a href="{{ route('courses.grades.edit', $course) }}" class="rounded-lg border border-blue-200 px-2.5 py-1.5 text-xs text-blue-700">Điểm</a>
                                @if ($canCreate)
                                    <a href="{{ route('courses.edit', $course) }}" class="rounded-lg border border-amber-200 px-2.5 py-1.5 text-xs text-amber-700">Sửa</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-slate-500">Không có lớp học phần phù hợp.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-4 py-3">{{ $courses->links() }}</div>
    </div>
@endsection
