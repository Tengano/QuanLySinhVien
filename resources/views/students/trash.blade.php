@extends('layouts.app')

@section('title', 'Thùng rác sinh viên')
@section('subtitle', 'Khôi phục hoặc xoá vĩnh viễn hồ sơ')

@section('content')
    <div class="mb-5 flex items-center justify-between">
        <a href="{{ route('students.index') }}" class="text-sm text-blue-700 hover:underline">← Danh sách sinh viên</a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500"><tr><th class="px-4 py-3">Mã SV</th><th class="px-4 py-3">Họ tên</th><th class="px-4 py-3">Lớp</th><th class="px-4 py-3">Đã xoá lúc</th><th class="px-4 py-3 text-right">Thao tác</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                @forelse ($students as $student)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $student->ma_sv }}</td>
                        <td class="px-4 py-3">{{ $student->ho_ten }}</td>
                        <td class="px-4 py-3">{{ $student->lop?->ten_lop }}</td>
                        <td class="px-4 py-3">{{ $student->deleted_at?->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <form method="POST" action="{{ route('students.restore', $student) }}">@csrf<button class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white">Khôi phục</button></form>
                                @if (auth()->user()->role === \App\Enums\Role::ADMIN)
                                    <form method="POST" action="{{ route('students.force-delete', $student) }}" onsubmit="return confirm('Xoá vĩnh viễn không thể khôi phục?')">@csrf @method('DELETE')<button class="rounded-lg border border-red-200 px-3 py-1.5 text-xs text-red-600">Xoá vĩnh viễn</button></form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-slate-500">Thùng rác trống.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-4 py-3">{{ $students->links() }}</div>
    </div>
@endsection
