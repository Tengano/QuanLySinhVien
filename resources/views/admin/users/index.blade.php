@extends('layouts.app')

@section('title', 'Người dùng')
@section('subtitle', 'Tài khoản và phân quyền hệ thống')

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold">Danh sách tài khoản</h2>
            <p class="text-sm text-slate-500">{{ $users->total() }} tài khoản</p>
        </div>
        <a href="{{ route('users.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">+ Thêm tài khoản</a>
    </div>

    <form method="GET" class="mb-4 flex flex-wrap items-end gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="min-w-[220px] flex-1">
            <label class="mb-1 block text-xs font-medium text-slate-500">Tìm kiếm</label>
            <input name="q" value="{{ request('q') }}" placeholder="Tên hoặc email..." class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        </div>
        <div class="min-w-[160px]">
            <label class="mb-1 block text-xs font-medium text-slate-500">Vai trò</label>
            <select name="role" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">Tất cả</option>
                @foreach (\App\Enums\Role::options() as $value => $label)
                    <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-[160px]">
            <label class="mb-1 block text-xs font-medium text-slate-500">Trạng thái</label>
            <select name="trang_thai" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">Tất cả</option>
                <option value="1" @selected(request('trang_thai') === '1')>Đang hoạt động</option>
                <option value="0" @selected(request('trang_thai') === '0')>Đã khoá</option>
            </select>
        </div>
        <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Lọc</button>
        <a href="{{ route('users.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600">Xoá lọc</a>
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr><th class="px-4 py-3">Họ tên</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Vai trò</th><th class="px-4 py-3">Trạng thái</th><th class="px-4 py-3">Đăng nhập cuối</th><th class="px-4 py-3 text-right">Thao tác</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse ($users as $user)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3">{{ $user->role->label() }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-1 text-xs font-medium {{ $user->trang_thai ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">{{ $user->trang_thai ? 'Hoạt động' : 'Đã khoá' }}</span>
                        </td>
                        <td class="px-4 py-3 text-slate-500">{{ $user->last_login_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap justify-end gap-1">
                                <a href="{{ route('users.edit', $user) }}" class="rounded-lg border border-slate-300 px-2.5 py-1.5 text-xs text-slate-600">Sửa</a>
                                <form method="POST" action="{{ route('users.reset', $user) }}">@csrf<button class="rounded-lg border border-amber-200 px-2.5 py-1.5 text-xs text-amber-700">Reset</button></form>
                                <form method="POST" action="{{ route('users.toggle', $user) }}">@csrf @method('PATCH')<button class="rounded-lg border border-blue-200 px-2.5 py-1.5 text-xs text-blue-700">{{ $user->trang_thai ? 'Khoá' : 'Mở' }}</button></form>
                                <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Xoá tài khoản này?')">@csrf @method('DELETE')<button class="rounded-lg border border-red-200 px-2.5 py-1.5 text-xs text-red-600">Xoá</button></form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">Không có tài khoản phù hợp.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-4 py-3">{{ $users->links() }}</div>
    </div>
@endsection
