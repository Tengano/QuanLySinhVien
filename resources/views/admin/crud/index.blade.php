@extends('layouts.app')

@section('title', $config['title'])
@section('subtitle', $config['subtitle'] ?? 'Quản lý danh mục')

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold">{{ $config['title'] }}</h2>
            <p class="text-sm text-slate-500">Tổng cộng {{ $items->total() }} bản ghi</p>
        </div>
        <a href="{{ route($config['route'].'.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
            + Thêm mới
        </a>
    </div>

    <div class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div class="min-w-[220px] flex-1">
                <label class="mb-1 block text-xs font-medium text-slate-500">Tìm kiếm</label>
                <input name="q" value="{{ request('q') }}" placeholder="{{ $config['search_placeholder'] ?? 'Nhập từ khoá...' }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            @foreach ($config['filters'] ?? [] as $filter)
                <div class="min-w-[180px]">
                    <label class="mb-1 block text-xs font-medium text-slate-500">{{ $filter['label'] }}</label>
                    <select name="{{ $filter['name'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <option value="">Tất cả</option>
                        @foreach ($filter['options'] as $value => $label)
                            <option value="{{ $value }}" @selected((string) request($filter['name']) === (string) $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            @endforeach

            <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Lọc</button>
            <a href="{{ route($config['route'].'.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600">Xoá lọc</a>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    @foreach ($config['columns'] as $column)
                        <th class="whitespace-nowrap px-4 py-3">{{ $column['label'] }}</th>
                    @endforeach
                    <th class="px-4 py-3 text-right">Thao tác</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse ($items as $item)
                    <tr class="hover:bg-slate-50">
                        @foreach ($config['columns'] as $column)
                            @php($value = data_get($item, $column['key']))
                            <td class="whitespace-nowrap px-4 py-3">
                                @if ($value instanceof \BackedEnum)
                                    {{ $value->label() }}
                                @elseif (($column['type'] ?? '') === 'date' && $value)
                                    {{ $value->format('d/m/Y') }}
                                @else
                                    {{ $value === null || $value === '' ? '—' : $value }}
                                @endif
                            </td>
                        @endforeach
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            <a href="{{ route($config['route'].'.edit', $item) }}" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-100">Sửa</a>
                            <form method="POST" action="{{ route($config['route'].'.destroy', $item) }}" class="inline" onsubmit="return confirm('Bạn chắc chắn muốn xoá bản ghi này?')">
                                @csrf
                                @method('DELETE')
                                <button class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50">Xoá</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($config['columns']) + 1 }}" class="px-4 py-10 text-center text-slate-500">Chưa có dữ liệu phù hợp.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-4 py-3">{{ $items->links() }}</div>
    </div>
@endsection
