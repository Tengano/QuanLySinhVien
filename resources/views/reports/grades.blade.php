@extends('layouts.print')

@section('title', 'Bảng điểm lớp học phần')

@section('content')
    <div class="header">
        <h1>Hệ thống quản lý sinh viên</h1>
        <p class="muted">BẢNG ĐIỂM LỚP HỌC PHẦN</p>
    </div>

    <div class="meta">
        <div><strong>Mã lớp HP:</strong> {{ $course->ma_lhp }}</div>
        <div><strong>Môn học:</strong> {{ $course->subject?->ten_mh }}</div>
        <div><strong>Học kỳ:</strong> {{ $course->semester?->ten_hoc_ky }} · {{ $course->semester?->nam_hoc }}</div>
        <div><strong>Giảng viên:</strong> {{ $course->giangVien?->name ?? 'Chưa phân công' }}</div>
    </div>

    <table>
        <thead><tr><th>STT</th><th>Mã SV</th><th>Họ tên</th><th>Lớp</th><th>QT</th><th>Thi</th><th>TB</th><th>Chữ</th><th>Hệ 4</th><th>Kết quả</th></tr></thead>
        <tbody>
        @forelse ($course->enrollments as $index => $enrollment)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $enrollment->student?->ma_sv }}</td>
                <td>{{ $enrollment->student?->ho_ten }}</td>
                <td>{{ $enrollment->student?->lop?->ten_lop }}</td>
                <td>{{ $enrollment->grade?->diem_qua_trinh ?? '—' }}</td>
                <td>{{ $enrollment->grade?->diem_thi ?? '—' }}</td>
                <td>{{ $enrollment->grade?->diem_tb ?? '—' }}</td>
                <td>{{ $enrollment->grade?->diem_chu ?? '—' }}</td>
                <td>{{ $enrollment->grade?->diem_he4 ?? '—' }}</td>
                <td>{{ $enrollment->grade?->dat ? 'Đạt' : 'Không đạt' }}</td>
            </tr>
        @empty
            <tr><td colspan="10">Chưa có sinh viên ghi danh.</td></tr>
        @endforelse
        </tbody>
    </table>
@endsection
