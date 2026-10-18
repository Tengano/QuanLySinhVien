@extends('layouts.app')

@section('title', 'Thông báo')
@section('subtitle', 'Gửi thông báo theo nhóm người nhận')

@section('content')
    <div class="mb-5 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold">Danh sách thông báo</h2>
            <p class="text-sm text-slate-500">{{ $announcements->total() }} thông báo</p>
        </div>
        <a href="{{ route('announcements.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white">+ Soạn thông báo</a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr><th class="px-4 py-3">Tiêu đề</th><th class="px-4 py-3">Đối tượng</th><th class="px-4 py-3">Người nhận</th><th class="px-4 py-3">Người gửi</th><th class="px-4 py-3">Thời gian</th><th class="px-4 py-3"></th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse ($announcements as $announcement)
                    <tr>
                        <td class="px-4 py-3"><p class="font-medium">{{ $announcement->tieu_de }}</p><p class="text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($announcement->noi_dung, 100) }}</p></td>
                        <td class="px-4 py-3">{{ $announcement->doi_tuong->label() }}</td>
                        <td class="px-4 py-3">{{ $announcement->users_count }}</td>
                        <td class="px-4 py-3">{{ $announcement->nguoiGui?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $announcement->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="{{ route('announcements.destroy', $announcement) }}" onsubmit="return confirm('Xoá thông báo này?')">@csrf @method('DELETE')<button class="rounded-lg border border-red-200 px-2.5 py-1.5 text-xs text-red-600">Xoá</button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">Chưa có thông báo.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-4 py-3">{{ $announcements->links() }}</div>
    </div>
@endsection
