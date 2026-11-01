@extends('bo-cuc.chinh')

@section('title', 'Bảng điều khiển')
@section('subtitle', 'Tổng quan hệ thống quản lý sinh viên')

@section('content')
    @if ($user->vai_tro === \App\Enums\VaiTro::SINH_VIEN)
        @include('bang-dieu-khien._sinh-vien')
    @elseif ($user->vai_tro === \App\Enums\VaiTro::GIANG_VIEN)
        @include('bang-dieu-khien._giang-vien')
    @else
        @include('bang-dieu-khien._quan-tri')
    @endif
@endsection
