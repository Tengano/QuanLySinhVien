@extends('bo-cuc.khach')

@section('title', 'Đăng nhập')

@section('content')
    <div class="w-full max-w-md">
        <div class="mb-6 text-center text-white">
            <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-600 text-xl font-bold">SV</div>
            <h1 class="text-2xl font-semibold">Quản lý sinh viên</h1>
            <p class="mt-1 text-sm text-slate-300">Đăng nhập để tiếp tục</p>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-xl">
            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="mb-1 block text-sm font-medium text-slate-700">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                           placeholder="Tài khoản">
                </div>

                <div>
                    <label for="password" class="mb-1 block text-sm font-medium text-slate-700">Mật khẩu</label>
                    <input id="password" name="password" type="password" required
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                           placeholder="••••••••">
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" value="1" class="rounded border-slate-300">
                    Ghi nhớ đăng nhập
                </label>

                <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                    Đăng nhập
                </button>
            </form>

        </div>
    </div>
@endsection
