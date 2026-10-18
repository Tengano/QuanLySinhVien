@extends('layouts.app')

@section('title', $course ? 'Sửa lớp học phần' : 'Mở lớp học phần')
@section('subtitle', 'Thông tin lớp HP và phân công giảng viên')

@section('content')
    <div class="mx-auto max-w-3xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ $course ? route('courses.update', $course) : route('courses.store') }}">
            @csrf
            @if ($course) @method('PUT') @endif

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium">Mã lớp HP <span class="text-red-500">*</span></label>
                    <input name="ma_lhp" value="{{ old('ma_lhp', $course?->ma_lhp) }}" required placeholder="VD: LTW-HK1-2026-N01" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Môn học <span class="text-red-500">*</span></label>
                    <select name="subject_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <option value="">— Chọn môn —</option>
                        @foreach ($subjects as $id => $name)<option value="{{ $id }}" @selected((string) old('subject_id', $course?->subject_id) === (string) $id)>{{ $name }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Học kỳ <span class="text-red-500">*</span></label>
                    <select name="semester_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <option value="">— Chọn học kỳ —</option>
                        @foreach ($semesters as $semester)<option value="{{ $semester->id }}" @selected((string) old('semester_id', $course?->semester_id) === (string) $semester->id)>{{ $semester->ten_hoc_ky }} · {{ $semester->nam_hoc }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Giảng viên</label>
                    <select name="giang_vien_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <option value="">— Chưa phân công —</option>
                        @foreach ($teachers as $id => $name)<option value="{{ $id }}" @selected((string) old('giang_vien_id', $course?->giang_vien_id) === (string) $id)>{{ $name }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Phòng học</label>
                    <input name="phong_hoc" value="{{ old('phong_hoc', $course?->phong_hoc) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Lịch học</label>
                    <input name="lich_hoc" value="{{ old('lich_hoc', $course?->lich_hoc) }}" placeholder="VD: Thứ 2, tiết 1-3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Sĩ số tối đa <span class="text-red-500">*</span></label>
                    <input type="number" name="so_sv_toi_da" min="1" max="200" value="{{ old('so_sv_toi_da', $course?->so_sv_toi_da ?? 50) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Trạng thái <span class="text-red-500">*</span></label>
                    <select name="trang_thai" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        @foreach ($statuses as $value => $label)<option value="{{ $value }}" @selected(old('trang_thai', $course?->trang_thai?->value ?? 'mo') === $value)>{{ $label }}</option>@endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium">Ghi chú</label>
                    <textarea name="ghi_chu" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('ghi_chu', $course?->ghi_chu) }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('courses.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600">Huỷ</a>
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white">Lưu lớp học phần</button>
            </div>
        </form>
    </div>
@endsection
