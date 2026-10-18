@extends('layouts.app')

@section('title', 'Hồ sơ cá nhân')
@section('subtitle', 'Cập nhật thông tin và mật khẩu')

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data"
                  class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                @csrf
                @method('PUT')

                <h2 class="mb-4 text-base font-semibold">Thông tin cá nhân</h2>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium">Họ tên</label>
                        <input name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Số điện thoại</label>
                        <input name="so_dien_thoai" value="{{ old('so_dien_thoai', $user->so_dien_thoai) }}"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Ảnh đại diện</label>
                        <input type="file" name="avatar" accept=".jpg,.jpeg,.png"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <p class="mt-1 text-xs text-slate-500">JPG/PNG, tối đa 2 MB.</p>
                    </div>
                </div>

                <div class="mt-5 flex justify-end">
                    <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Lưu thay đổi</button>
                </div>
            </form>

            <form method="POST" action="{{ route('profile.password') }}"
                  class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                @csrf
                @method('PUT')

                <h2 class="mb-4 text-base font-semibold">Đổi mật khẩu</h2>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="mb-1 block text-sm font-medium">Mật khẩu hiện tại</label>
                        <input type="password" name="current_password" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Mật khẩu mới</label>
                        <input type="password" name="password" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Xác nhận mật khẩu</label>
                        <input type="password" name="password_confirmation" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                </div>

                <div class="mt-5 flex justify-end">
                    <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Đổi mật khẩu</button>
                </div>
            </form>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm">
            @if ($user->avatar)
                <img src="{{ asset('storage/'.$user->avatar) }}" alt="Avatar" class="mx-auto h-28 w-28 rounded-full object-cover">
            @else
                <div class="mx-auto flex h-28 w-28 items-center justify-center rounded-full bg-blue-100 text-3xl font-bold text-blue-700">
                    {{ mb_substr($user->name, 0, 1) }}
                </div>
            @endif
            <p class="mt-4 text-lg font-semibold">{{ $user->name }}</p>
            <p class="text-sm text-slate-500">{{ $user->email }}</p>
            <span class="mt-3 inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">{{ $user->role->label() }}</span>
        </div>
    </div>
@endsection
