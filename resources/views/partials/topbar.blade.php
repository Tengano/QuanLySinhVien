@php
    $user = auth()->user();
    $unread = $user ? $user->announcements()->wherePivot('da_doc', false)->count() : 0;
@endphp

<header class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 bg-white px-4 py-3 lg:px-6">
    <div>
        <h1 class="text-lg font-semibold text-slate-900">@yield('title', 'Dashboard')</h1>
        <p class="text-xs text-slate-500">@yield('subtitle', 'Hệ thống quản lý sinh viên')</p>
    </div>

    <div class="flex items-center gap-3">
        <a href="{{ route('inbox.index') }}" class="relative rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-600 hover:bg-slate-50">
            Thông báo
            @if ($unread > 0)
                <span class="absolute -right-1 -top-1 rounded-full bg-red-500 px-1.5 py-0.5 text-[10px] font-bold text-white">{{ $unread }}</span>
            @endif
        </a>

        <div class="hidden text-right sm:block">
            <p class="text-sm font-medium text-slate-800">{{ $user?->name }}</p>
            <p class="text-xs text-slate-500">{{ $user?->role?->label() }}</p>
        </div>

        <a href="{{ route('profile.edit') }}" class="rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-600 hover:bg-slate-50">Hồ sơ</a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="rounded-lg bg-slate-900 px-3 py-2 text-sm font-medium text-white hover:bg-slate-700">Đăng xuất</button>
        </form>
    </div>
</header>
