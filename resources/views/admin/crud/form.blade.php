@extends('layouts.app')

@section('title', ($record ? 'Sửa ' : 'Thêm ').$config['title'])
@section('subtitle', 'Biểu mẫu danh mục')

@section('content')
    <div class="mx-auto max-w-3xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST"
              action="{{ $record ? route($config['route'].'.update', $record) : route($config['route'].'.store') }}">
            @csrf
            @if ($record)
                @method('PUT')
            @endif

            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($config['fields'] as $field)
                    @php($name = $field['name'])
                    @php($raw = old($name, $record ? data_get($record, $name) : ($field['default'] ?? null)))
                    @php($value = $raw instanceof \BackedEnum ? $raw->value : $raw)
                    @php($wide = $field['wide'] ?? in_array($field['type'] ?? 'text', ['textarea'], true))

                    <div class="{{ $wide ? 'sm:col-span-2' : '' }}">
                        <label for="{{ $name }}" class="mb-1 block text-sm font-medium text-slate-700">
                            {{ $field['label'] }}
                            @if ($field['required'] ?? false)<span class="text-red-500">*</span>@endif
                        </label>

                        @if (($field['type'] ?? 'text') === 'select')
                            <select id="{{ $name }}" name="{{ $name }}" @if ($field['required'] ?? false) required @endif
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                @if ($field['empty'] ?? true)
                                    <option value="">— Chọn {{ mb_strtolower($field['label']) }} —</option>
                                @endif
                                @foreach ($field['options'] as $optionValue => $optionLabel)
                                    <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
                                @endforeach
                            </select>
                        @elseif (($field['type'] ?? 'text') === 'textarea')
                            <textarea id="{{ $name }}" name="{{ $name }}" rows="3" @if ($field['required'] ?? false) required @endif
                                      class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ $value }}</textarea>
                        @elseif (($field['type'] ?? 'text') === 'checkbox')
                            <input type="hidden" name="{{ $name }}" value="0">
                            <label class="flex items-center gap-2 text-sm text-slate-600">
                                <input id="{{ $name }}" type="checkbox" name="{{ $name }}" value="1" @checked((bool) $value)>
                                {{ $field['help'] ?? 'Bật' }}
                            </label>
                        @else
                            <input id="{{ $name }}" name="{{ $name }}" type="{{ $field['type'] ?? 'text' }}"
                                   value="{{ $value instanceof \Carbon\CarbonInterface ? $value->format('Y-m-d') : $value }}"
                                   @if ($field['required'] ?? false) required @endif
                                   @isset($field['min']) min="{{ $field['min'] }}" @endisset
                                   @isset($field['max']) max="{{ $field['max'] }}" @endisset
                                   @isset($field['step']) step="{{ $field['step'] }}" @endisset
                                   placeholder="{{ $field['placeholder'] ?? '' }}"
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        @endif

                        @error($name)
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route($config['route'].'.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600">Huỷ</a>
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    {{ $record ? 'Lưu thay đổi' : 'Thêm mới' }}
                </button>
            </div>
        </form>
    </div>
@endsection
