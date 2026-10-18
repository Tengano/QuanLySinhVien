@extends('layouts.app')

@section('title', $user ? 'Sửa tài khoản' : 'Thêm tài khoản')
@section('subtitle', 'Quản lý tài khoản người dùng')

@section('content')
    <div class="mx-auto max-w-2xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ $user ? route('users.update', $user) : route('users.store') }}">
            @csrf
            @if ($user) @method('PUT') @endif

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium">Họ tên <span class="text-red-500">*</span></label>
                    <input name="name" value="{{ old('name', $user?->name) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user?->email) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Vai trò <span class="text-red-500">*</span></label>
                    <select name="role" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        @foreach (\App\Enums\Role::options() as $value => $label)
                            <option value="{{ $value }}" @selected(old('role', $user?->role?->value) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Số điện thoại</label>
                    <input name="so_dien_thoai" value="{{ old('so_dien_thoai', $user?->so_dien_thoai) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Mật khẩu {{ $user ? '(để trống nếu giữ nguyên)' : '' }} @if(! $user)<span class="text-red-500">*</span>@endif</label>
                    <input type="password" name="password" @if(! $user) required @endif class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Xác nhận mật khẩu</label>
                    <input type="password" name="password_confirmation" @if(! $user) required @endif class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
            </div>

            <label class="mt-4 flex items-center gap-2 text-sm text-slate-600">
                <input type="hidden" name="trang_thai" value="0">
                <input type="checkbox" name="trang_thai" value="1" @checked(old('trang_thai', $user?->trang_thai ?? true))>
                Tài khoản đang hoạt động
            </label>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('users.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600">Huỷ</a>
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white">Lưu</button>
            </div>
        </form>
    </div>
@endsection
