@php
    $user = auth()->user();
    $role = $user?->role;
    $link = 'flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition';
    $active = 'bg-blue-600 text-white shadow-sm';
    $normal = 'text-slate-300 hover:bg-slate-800 hover:text-white';
@endphp

<aside class="w-full border-b border-slate-800 bg-slate-950 lg:min-h-screen lg:w-64 lg:border-b-0 lg:border-r">
    <div class="flex items-center justify-between px-4 py-4">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-white">
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-600 text-sm font-bold">SV</span>
            <span>
                <span class="block text-sm font-semibold">Quản lý sinh viên</span>
                <span class="block text-xs text-slate-400">Học vụ số</span>
            </span>
        </a>
    </div>

    <nav class="flex flex-wrap gap-1 px-3 pb-4 lg:flex-col">
        <a href="{{ route('dashboard') }}" class="{{ $link }} {{ request()->routeIs('dashboard') ? $active : $normal }}">
            <span>▦</span> Dashboard
        </a>

        @if ($role === \App\Enums\Role::ADMIN)
            <a href="{{ route('users.index') }}" class="{{ $link }} {{ request()->routeIs('users.*') ? $active : $normal }}">
                <span>♟</span> Người dùng
            </a>
        @endif

        @if (in_array($role, [\App\Enums\Role::ADMIN, \App\Enums\Role::GIAO_VU], true))
            <p class="mt-3 w-full px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Danh mục</p>
            <a href="{{ route('danh-muc.khoa.index') }}" class="{{ $link }} {{ request()->routeIs('danh-muc.khoa.*') ? $active : $normal }}">Khoa</a>
            <a href="{{ route('danh-muc.nganh.index') }}" class="{{ $link }} {{ request()->routeIs('danh-muc.nganh.*') ? $active : $normal }}">Ngành</a>
            <a href="{{ route('danh-muc.lop.index') }}" class="{{ $link }} {{ request()->routeIs('danh-muc.lop.*') ? $active : $normal }}">Lớp hành chính</a>
            <a href="{{ route('danh-muc.mon-hoc.index') }}" class="{{ $link }} {{ request()->routeIs('danh-muc.mon-hoc.*') ? $active : $normal }}">Môn học</a>
            <a href="{{ route('danh-muc.hoc-ky.index') }}" class="{{ $link }} {{ request()->routeIs('danh-muc.hoc-ky.*') ? $active : $normal }}">Học kỳ</a>

            <p class="mt-3 w-full px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Nghiệp vụ</p>
            <a href="{{ route('students.index') }}" class="{{ $link }} {{ request()->routeIs('students.*') ? $active : $normal }}">Sinh viên</a>
            <a href="{{ route('courses.index') }}" class="{{ $link }} {{ request()->routeIs('courses.*') ? $active : $normal }}">Lớp học phần</a>
            <a href="{{ route('announcements.index') }}" class="{{ $link }} {{ request()->routeIs('announcements.*') ? $active : $normal }}">Thông báo</a>
        @elseif ($role === \App\Enums\Role::GIANG_VIEN)
            <p class="mt-3 w-full px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Giảng dạy</p>
            <a href="{{ route('courses.index') }}" class="{{ $link }} {{ request()->routeIs('courses.*') ? $active : $normal }}">Lớp học phần</a>
            <a href="{{ route('inbox.index') }}" class="{{ $link }} {{ request()->routeIs('inbox.*') ? $active : $normal }}">Hộp thư</a>
        @elseif ($role === \App\Enums\Role::SINH_VIEN)
            <p class="mt-3 w-full px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Học tập</p>
            <a href="{{ route('portal.grades') }}" class="{{ $link }} {{ request()->routeIs('portal.*') ? $active : $normal }}">Bảng điểm của tôi</a>
            <a href="{{ route('inbox.index') }}" class="{{ $link }} {{ request()->routeIs('inbox.*') ? $active : $normal }}">Hộp thư</a>
        @endif
    </nav>
</aside>
