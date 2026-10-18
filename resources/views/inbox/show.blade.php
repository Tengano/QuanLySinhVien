@extends('layouts.app')

@section('title', 'Chi tiết thông báo')
@section('subtitle', $announcement->created_at->format('d/m/Y H:i'))

@section('content')
    <div class="mx-auto max-w-3xl">
        <a href="{{ route('inbox.index') }}" class="text-sm text-blue-700 hover:underline">← Hộp thư</a>

        <article class="mt-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h1 class="text-xl font-semibold">{{ $announcement->tieu_de }}</h1>
            <p class="mt-1 text-xs text-slate-500">Người gửi: {{ $announcement->nguoiGui?->name ?? 'Hệ thống' }} · {{ $announcement->created_at->format('d/m/Y H:i') }}</p>
            <div class="mt-5 whitespace-pre-line border-t border-slate-100 pt-5 text-sm leading-6 text-slate-700">{{ $announcement->noi_dung }}</div>
        </article>
    </div>
@endsection
