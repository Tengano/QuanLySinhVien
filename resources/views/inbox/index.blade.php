@extends('layouts.app')

@section('title', 'Hộp thư')
@section('subtitle', 'Thông báo gửi đến bạn')

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold">Hộp thư thông báo</h2>
            <p class="text-sm text-slate-500">{{ $announcements->total() }} thông báo</p>
        </div>
        <form method="POST" action="{{ route('inbox.mark-all') }}">
            @csrf
            <button class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700">Đánh dấu tất cả đã đọc</button>
        </form>
    </div>

    <div class="space-y-3">
        @forelse ($announcements as $announcement)
            <a href="{{ route('inbox.show', $announcement) }}" class="block rounded-2xl border bg-white p-5 shadow-sm transition hover:border-blue-300 {{ $announcement->pivot->da_doc ? 'border-slate-200' : 'border-blue-300' }}">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            @unless ($announcement->pivot->da_doc)
                                <span class="h-2 w-2 rounded-full bg-blue-600"></span>
                            @endunless
                            <h3 class="font-semibold {{ $announcement->pivot->da_doc ? 'text-slate-700' : 'text-slate-950' }}">{{ $announcement->tieu_de }}</h3>
                        </div>
                        <p class="mt-1 text-sm text-slate-500">{{ \Illuminate\Support\Str::limit($announcement->noi_dung, 160) }}</p>
                    </div>
                    <div class="text-right text-xs text-slate-500">
                        <p>{{ $announcement->nguoiGui?->name ?? 'Hệ thống' }}</p>
                        <p>{{ $announcement->created_at->format('d/m/Y H:i') }}</p>
                        @unless ($announcement->pivot->da_doc)
                            <span class="mt-1 inline-flex rounded-full bg-blue-100 px-2 py-0.5 font-medium text-blue-700">Chưa đọc</span>
                        @endunless
                    </div>
                </div>
            </a>
        @empty
            <div class="rounded-2xl border border-slate-200 bg-white p-10 text-center text-slate-500">Hộp thư trống.</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $announcements->links() }}</div>
@endsection
