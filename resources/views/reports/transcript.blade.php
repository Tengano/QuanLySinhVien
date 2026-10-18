@extends('layouts.print')

@section('title', 'Bảng điểm sinh viên')

@section('content')
    <div class="header">
        <h1>Hệ thống quản lý sinh viên</h1>
        <p class="muted">BẢNG ĐIỂM SINH VIÊN</p>
    </div>

    <div class="meta">
        <div><strong>Họ tên:</strong> {{ $student->ho_ten }}</div>
        <div><strong>Mã sinh viên:</strong> {{ $student->ma_sv }}</div>
        <div><strong>Lớp:</strong> {{ $student->lop?->ten_lop }}</div>
        <div><strong>Ngành:</strong> {{ $student->lop?->major?->ten_nganh }}</div>
        <div><strong>Khoa:</strong> {{ $student->lop?->major?->faculty?->ten_khoa }}</div>
        <div><strong>Ngày sinh:</strong> {{ $student->ngay_sinh?->format('d/m/Y') }}</div>
    </div>

    <table>
        <thead><tr><th>Học kỳ</th><th>Môn học</th><th>TC</th><th>QT</th><th>Thi</th><th>TB</th><th>Chữ</th><th>Hệ 4</th><th>Kết quả</th></tr></thead>
        <tbody>
        @forelse ($student->enrollments as $enrollment)
            <tr>
                <td>{{ $enrollment->course->semester?->ten_hoc_ky }} · {{ $enrollment->course->semester?->nam_hoc }}</td>
                <td>{{ $enrollment->course->subject?->ten_mh }}</td>
                <td>{{ $enrollment->course->subject?->so_tin_chi }}</td>
                <td>{{ $enrollment->grade?->diem_qua_trinh ?? '—' }}</td>
                <td>{{ $enrollment->grade?->diem_thi ?? '—' }}</td>
                <td>{{ $enrollment->grade?->diem_tb ?? '—' }}</td>
                <td>{{ $enrollment->grade?->diem_chu ?? '—' }}</td>
                <td>{{ $enrollment->grade?->diem_he4 ?? '—' }}</td>
                <td>{{ $enrollment->grade?->dat ? 'Đạt' : 'Không đạt' }}</td>
            </tr>
        @empty
            <tr><td colspan="9">Chưa có dữ liệu điểm.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div class="summary">
        GPA tích luỹ: {{ number_format($gpa['gpa'], 2) }} ·
        Tín chỉ đạt: {{ $gpa['tin_chi_dat'] }}/{{ $gpa['tong_tin_chi'] }} ·
        Xếp loại: {{ $gpa['xep_loai'] }}
    </div>
@endsection
