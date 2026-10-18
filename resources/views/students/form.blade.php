@extends('layouts.app')

@section('title', $student ? 'Sửa hồ sơ sinh viên' : 'Thêm sinh viên')
@section('subtitle', 'Thông tin hồ sơ và tài khoản')

@section('content')
    <div class="mx-auto max-w-4xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ $student ? route('students.update', $student) : route('students.store') }}" enctype="multipart/form-data">
            @csrf
            @if ($student) @method('PUT') @endif

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium">Mã sinh viên <span class="text-red-500">*</span></label>
                    <input name="ma_sv" value="{{ old('ma_sv', $student?->ma_sv) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Họ tên <span class="text-red-500">*</span></label>
                    <input name="ho_ten" value="{{ old('ho_ten', $student?->ho_ten) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Ngày sinh <span class="text-red-500">*</span></label>
                    <input type="date" name="ngay_sinh" value="{{ old('ngay_sinh', $student?->ngay_sinh?->format('Y-m-d')) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Giới tính <span class="text-red-500">*</span></label>
                    <select name="gioi_tinh" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        @foreach ($gioiTinhs as $value => $label)
                            <option value="{{ $value }}" @selected(old('gioi_tinh', $student?->gioi_tinh?->value) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Lớp <span class="text-red-500">*</span></label>
                    <select name="lop_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <option value="">— Chọn lớp —</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" @selected((string) old('lop_id', $student?->lop_id) === (string) $class->id)>{{ $class->ma_lop }} — {{ $class->ten_lop }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Trạng thái <span class="text-red-500">*</span></label>
                    <select name="trang_thai" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" @selected(old('trang_thai', $student?->trang_thai?->value ?? 'dang_hoc') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Email</label>
                    <input type="email" name="email" value="{{ old('email', $student?->email) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Số điện thoại</label>
                    <input name="sdt" value="{{ old('sdt', $student?->sdt) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">CCCD</label>
                    <input name="cccd" value="{{ old('cccd', $student?->cccd) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Ngày nhập học</label>
                    <input type="date" name="ngay_nhap_hoc" value="{{ old('ngay_nhap_hoc', $student?->ngay_nhap_hoc?->format('Y-m-d')) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium">Địa chỉ</label>
                    <textarea name="dia_chi" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('dia_chi', $student?->dia_chi) }}</textarea>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Ảnh đại diện</label>
                    <input type="file" name="anh" accept=".jpg,.jpeg,.png" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <p class="mt-1 text-xs text-slate-500">JPG/PNG, tối đa 2 MB.</p>
                    @if ($student?->anh)
                        <img src="{{ asset('storage/'.$student->anh) }}" alt="" class="mt-2 h-20 w-20 rounded-lg object-cover">
                    @endif
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Ghi chú</label>
                    <textarea name="ghi_chu" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('ghi_chu', $student?->ghi_chu) }}</textarea>
                </div>
            </div>

            @if (! $student?->user_id)
                <label class="mt-4 flex items-center gap-2 rounded-lg bg-blue-50 p-3 text-sm text-blue-800">
                    <input type="checkbox" name="tao_tai_khoan" value="1" @checked(old('tao_tai_khoan'))>
                    Tạo tài khoản đăng nhập cho sinh viên (cần có email)
                </label>
            @else
                <p class="mt-4 rounded-lg bg-emerald-50 p-3 text-sm text-emerald-800">Đã liên kết tài khoản: {{ $student->user?->email }}</p>
            @endif

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('students.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600">Huỷ</a>
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white">Lưu hồ sơ</button>
            </div>
        </form>
    </div>
@endsection
