@extends('layouts.app')

@section('title', 'Soạn thông báo')
@section('subtitle', 'Chọn đối tượng nhận chính xác')

@section('content')
    <div class="mx-auto max-w-3xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('announcements.store') }}">
            @csrf

            <div class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium">Tiêu đề <span class="text-red-500">*</span></label>
                    <input name="tieu_de" value="{{ old('tieu_de') }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Nội dung <span class="text-red-500">*</span></label>
                    <textarea name="noi_dung" rows="6" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('noi_dung') }}</textarea>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Đối tượng nhận <span class="text-red-500">*</span></label>
                    <select name="doi_tuong" id="doi_tuong" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        @foreach ($targets as $value => $label)
                            <option value="{{ $value }}" @selected(old('doi_tuong', 'toan_truong') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div data-target-group="khoa">
                    <label class="mb-1 block text-sm font-medium">Chọn Khoa</label>
                    <select name="khoa_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <option value="">— Chọn khoa —</option>
                        @foreach ($faculties as $id => $name)<option value="{{ $id }}" @selected((string) old('khoa_id') === (string) $id)>{{ $name }}</option>@endforeach
                    </select>
                </div>
                <div data-target-group="lop">
                    <label class="mb-1 block text-sm font-medium">Chọn lớp hành chính</label>
                    <select name="lop_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <option value="">— Chọn lớp —</option>
                        @foreach ($classes as $id => $name)<option value="{{ $id }}" @selected((string) old('lop_id') === (string) $id)>{{ $name }}</option>@endforeach
                    </select>
                </div>
                <div data-target-group="lop_hoc_phan">
                    <label class="mb-1 block text-sm font-medium">Chọn lớp học phần</label>
                    <select name="lop_hp_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <option value="">— Chọn lớp HP —</option>
                        @foreach ($courses as $course)<option value="{{ $course->id }}" @selected((string) old('lop_hp_id') === (string) $course->id)>{{ $course->ma_lhp }} — {{ $course->subject?->ten_mh }}</option>@endforeach
                    </select>
                </div>
                <div data-target-group="nguoi_dung">
                    <label class="mb-1 block text-sm font-medium">Chọn người dùng</label>
                    <select name="nguoi_nhan_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <option value="">— Chọn người dùng —</option>
                        @foreach ($users as $id => $name)<option value="{{ $id }}" @selected((string) old('nguoi_nhan_id') === (string) $id)>{{ $name }}</option>@endforeach
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('announcements.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600">Huỷ</a>
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white">Gửi thông báo</button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            const target = document.getElementById('doi_tuong');
            const groups = document.querySelectorAll('[data-target-group]');
            const refresh = () => {
                groups.forEach((group) => {
                    group.style.display = group.dataset.targetGroup === target.value ? '' : 'none';
                });
            };
            target?.addEventListener('change', refresh);
            refresh();
        </script>
    @endpush
@endsection
