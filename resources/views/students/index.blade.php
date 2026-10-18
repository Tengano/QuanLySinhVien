@extends('layouts.app')

@section('title', 'Sinh viên')
@section('subtitle', 'Hồ sơ sinh viên và tài khoản tra cứu')

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold">Danh sách sinh viên</h2>
            <p class="text-sm text-slate-500">{{ $students->total() }} hồ sơ</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('students.import.form') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700">Import CSV</a>
            <a href="{{ route('students.export', request()->query()) }}" class="rounded-lg border border-emerald-300 px-3 py-2 text-sm text-emerald-700">Xuất Excel/CSV</a>
            <a href="{{ route('students.trash') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700">Thùng rác</a>
            <a href="{{ route('students.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">+ Thêm sinh viên</a>
        </div>
    </div>

    @if (session('import_errors'))
        <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
            <p class="font-semibold">Các dòng import bị lỗi:</p>
            <ul class="mt-2 max-h-40 list-inside list-disc overflow-y-auto">
                @foreach (session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="GET" class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-6">
            <div class="lg:col-span-2">
                <label class="mb-1 block text-xs font-medium text-slate-500">Tìm kiếm</label>
                <input name="q" value="{{ request('q') }}" placeholder="Mã SV, họ tên, email, SĐT..." class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-500">Khoa</label>
                <select name="khoa_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Tất cả</option>
                    @foreach ($faculties as $id => $name)<option value="{{ $id }}" @selected((string) request('khoa_id') === (string) $id)>{{ $name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-500">Ngành</label>
                <select name="nganh_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Tất cả</option>
                    @foreach ($majors as $id => $name)<option value="{{ $id }}" @selected((string) request('nganh_id') === (string) $id)>{{ $name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-500">Lớp</label>
                <select name="lop_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Tất cả</option>
                    @foreach ($classes as $id => $name)<option value="{{ $id }}" @selected((string) request('lop_id') === (string) $id)>{{ $name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-500">Trạng thái</label>
                <select name="trang_thai" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Tất cả</option>
                    @foreach ($statuses as $value => $label)<option value="{{ $value }}" @selected(request('trang_thai') === $value)>{{ $label }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-500">Giới tính</label>
                <select name="gioi_tinh" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Tất cả</option>
                    @foreach (\App\Enums\GioiTinh::options() as $value => $label)<option value="{{ $value }}" @selected(request('gioi_tinh') === $value)>{{ $label }}</option>@endforeach
                </select>
            </div>
        </div>
        <div class="mt-3 flex gap-2">
            <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Lọc</button>
            <a href="{{ route('students.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600">Xoá lọc</a>
        </div>
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr><th class="px-4 py-3">Sinh viên</th><th class="px-4 py-3">Lớp / Khoa</th><th class="px-4 py-3">Liên hệ</th><th class="px-4 py-3">Trạng thái</th><th class="px-4 py-3">Tài khoản</th><th class="px-4 py-3 text-right">Thao tác</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                                @forelse ($students as $student)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if ($student->anh)
                                    <img src="{{ asset('storage/'.$student->anh) }}" alt="" class="h-10 w-10 rounded-full object-cover">
                                @else
                                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700">{{ mb_substr($student->ho_ten, 0, 1) }}</span>
                                @endif
                                <div>
                                    <p class="font-medium">{{ $student->ho_ten }}</p>
                                    <p class="text-xs text-slate-500">{{ $student->ma_sv }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3"><p>{{ $student->lop?->ten_lop }}</p><p class="text-xs text-slate-500">{{ $student->lop?->major?->faculty?->ten_khoa }}</p></td>
                        <td class="px-4 py-3"><p>{{ $student->email ?? '—' }}</p><p class="text-xs text-slate-500">{{ $student->sdt ?? '—' }}</p></td>
                        <td class="px-4 py-3"><span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">{{ $student->trang_thai->label() }}</span></td>
                        <td class="px-4 py-3 text-xs">{{ $student->user ? 'Đã cấp' : 'Chưa cấp' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-1">
                                <a href="{{ route('students.show', $student) }}" class="rounded-lg border border-slate-300 px-2.5 py-1.5 text-xs text-slate-600">Xem</a>
                                <a href="{{ route('students.edit', $student) }}" class="rounded-lg border border-blue-200 px-2.5 py-1.5 text-xs text-blue-700">Sửa</a>
                                <form method="POST" action="{{ route('students.destroy', $student) }}" onsubmit="return confirm('Xoá mềm hồ sơ này?')">@csrf @method('DELETE')<button class="rounded-lg border border-red-200 px-2.5 py-1.5 text-xs text-red-600">Xoá</button></form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">Không có sinh viên phù hợp.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-4 py-3">{{ $students->links() }}</div>
    </div>
@endsection
