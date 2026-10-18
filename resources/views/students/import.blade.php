@extends('layouts.app')

@section('title', 'Import sinh viên')
@section('subtitle', 'Nhập hàng loạt từ file CSV (mở được bằng Excel)')

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold">Hướng dẫn</h2>
            <ol class="mt-3 list-inside list-decimal space-y-1 text-sm text-slate-600">
                <li>Tải file mẫu, mở bằng Excel và điền dữ liệu.</li>
                <li>Giữ nguyên tên cột: <code>ma_sv, ho_ten, ngay_sinh, gioi_tinh, ma_lop, email, sdt, cccd, dia_chi</code>.</li>
                <li>Ngày sinh theo định dạng <code>YYYY-MM-DD</code>; giới tính <code>nam/nu/khac</code>; mã lớp phải tồn tại.</li>
                <li>Lưu file dưới dạng CSV UTF-8 rồi tải lên.</li>
            </ol>
            <a href="{{ route('students.template') }}" class="mt-4 inline-flex rounded-lg border border-emerald-300 px-4 py-2 text-sm font-medium text-emerald-700">Tải file mẫu CSV</a>
        </div>

        <form method="POST" action="{{ route('students.import') }}" enctype="multipart/form-data" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            <label class="mb-2 block text-sm font-medium">Chọn file CSV</label>
            <input type="file" name="file" accept=".csv,text/csv" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <p class="mt-2 text-xs text-slate-500">Tối đa 5 MB. Dòng lỗi sẽ được báo chi tiết, các dòng hợp lệ vẫn được nhập.</p>
            <div class="mt-5 flex justify-end gap-3">
                <a href="{{ route('students.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600">Huỷ</a>
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white">Bắt đầu import</button>
            </div>
        </form>
    </div>
@endsection
