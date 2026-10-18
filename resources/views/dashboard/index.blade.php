@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Tổng quan hệ thống quản lý sinh viên')

@section('content')
    @if ($user->role === \App\Enums\Role::SINH_VIEN)
        @include('dashboard._student')
    @elseif ($user->role === \App\Enums\Role::GIANG_VIEN)
        @include('dashboard._teacher')
    @else
        @include('dashboard._admin')
    @endif
@endsection
