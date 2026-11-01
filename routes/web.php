<?php

use App\Http\Controllers\ThongBaoController;
use App\Http\Controllers\Auth\DangNhapController;
use App\Http\Controllers\LopHocPhanController;
use App\Http\Controllers\BangDieuKhienController;
use App\Http\Controllers\GhiDanhController;
use App\Http\Controllers\KhoaController;
use App\Http\Controllers\DiemController;
use App\Http\Controllers\HopThuController;
use App\Http\Controllers\NganhController;
use App\Http\Controllers\CongSinhVienController;
use App\Http\Controllers\HoSoCaNhanController;
use App\Http\Controllers\BaoCaoController;
use App\Http\Controllers\LopController;
use App\Http\Controllers\HocKyController;
use App\Http\Controllers\SinhVienController;
use App\Http\Controllers\MonHocController;
use App\Http\Controllers\NguoiDungController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check()
    ? redirect()->route('dashboard')
    : redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [DangNhapController::class, 'create'])->name('login');
    Route::post('/login', [DangNhapController::class, 'store'])->middleware('throttle:login')->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [DangNhapController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [BangDieuKhienController::class, 'index'])->name('dashboard');

    Route::get('/profile', [HoSoCaNhanController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [HoSoCaNhanController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [HoSoCaNhanController::class, 'updatePassword'])->name('profile.password');

    // Các route dưới đây sẽ được thay bằng controller thật ở các bước tiếp theo.
    Route::middleware('role:admin')->prefix('users')->name('users.')->group(function () {
        Route::get('/', [NguoiDungController::class, 'index'])->name('index');
        Route::get('/create', [NguoiDungController::class, 'create'])->name('create');
        Route::post('/', [NguoiDungController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [NguoiDungController::class, 'edit'])->name('edit');
        Route::put('/{user}', [NguoiDungController::class, 'update'])->name('update');
        Route::delete('/{user}', [NguoiDungController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/reset-password', [NguoiDungController::class, 'resetPassword'])->name('reset');
        Route::patch('/{user}/toggle', [NguoiDungController::class, 'toggleStatus'])->name('toggle');
    });

    Route::middleware('role:admin,giao_vu')
        ->prefix('danh-muc')
        ->name('danh-muc.')
        ->group(function () {
            Route::resource('khoa', KhoaController::class)->except('show');
            Route::resource('nganh', NganhController::class)->except('show');
            Route::resource('lop', LopController::class)->except('show');
            Route::resource('mon-hoc', MonHocController::class)->except('show');
            Route::resource('hoc-ky', HocKyController::class)->except('show');
        });

    Route::middleware('role:admin,giao_vu')->prefix('sinh-vien')->name('students.')->group(function () {
        Route::get('/', [SinhVienController::class, 'index'])->name('index');
        Route::get('/create', [SinhVienController::class, 'create'])->name('create');
        Route::post('/', [SinhVienController::class, 'store'])->name('store');
        Route::get('/trash', [SinhVienController::class, 'trash'])->name('trash');
        Route::get('/import', [SinhVienController::class, 'importForm'])->name('import.form');
        Route::post('/import', [SinhVienController::class, 'import'])->name('import');
        Route::get('/template', [SinhVienController::class, 'template'])->name('template');
        Route::get('/export', [SinhVienController::class, 'export'])->name('export');
        Route::get('/{student}', [SinhVienController::class, 'show'])->whereNumber('student')->name('show');
        Route::get('/{student}/edit', [SinhVienController::class, 'edit'])->whereNumber('student')->name('edit');
        Route::put('/{student}', [SinhVienController::class, 'update'])->whereNumber('student')->name('update');
        Route::delete('/{student}', [SinhVienController::class, 'destroy'])->whereNumber('student')->name('destroy');
        Route::post('/{student}/restore', [SinhVienController::class, 'restore'])->whereNumber('student')->name('restore');
        Route::delete('/{student}/force', [SinhVienController::class, 'forceDelete'])->whereNumber('student')->name('force-delete');
    });

    Route::middleware('role:admin,giao_vu,giang_vien')->group(function () {
        Route::get('/lop-hoc-phan', [LopHocPhanController::class, 'index'])->name('courses.index');
        Route::get('/lop-hoc-phan/{course}', [LopHocPhanController::class, 'show'])->whereNumber('course')->name('courses.show');
        Route::get('/lop-hoc-phan/{course}/diem', [DiemController::class, 'edit'])->whereNumber('course')->name('courses.grades.edit');
        Route::put('/lop-hoc-phan/{course}/diem', [DiemController::class, 'update'])->whereNumber('course')->name('courses.grades.update');
        Route::post('/lop-hoc-phan/{course}/diem/chot', [DiemController::class, 'lock'])->whereNumber('course')->name('courses.grades.lock');
    });

    Route::middleware('role:admin,giao_vu')->group(function () {
        Route::get('/lop-hoc-phan/create', [LopHocPhanController::class, 'create'])->name('courses.create');
        Route::post('/lop-hoc-phan', [LopHocPhanController::class, 'store'])->name('courses.store');
        Route::get('/lop-hoc-phan/{course}/edit', [LopHocPhanController::class, 'edit'])->whereNumber('course')->name('courses.edit');
        Route::put('/lop-hoc-phan/{course}', [LopHocPhanController::class, 'update'])->whereNumber('course')->name('courses.update');
        Route::delete('/lop-hoc-phan/{course}', [LopHocPhanController::class, 'destroy'])->whereNumber('course')->name('courses.destroy');
        Route::post('/lop-hoc-phan/{course}/diem/mo-khoa', [DiemController::class, 'unlock'])->whereNumber('course')->name('courses.grades.unlock');
        Route::get('/lop-hoc-phan/{course}/ghi-danh', [GhiDanhController::class, 'create'])->whereNumber('course')->name('courses.enroll.create');
        Route::post('/lop-hoc-phan/{course}/ghi-danh', [GhiDanhController::class, 'store'])->whereNumber('course')->name('courses.enroll.store');
        Route::delete('/ghi-danh/{enrollment}', [GhiDanhController::class, 'destroy'])->whereNumber('enrollment')->name('enrollments.destroy');
    });

    Route::middleware('role:admin,giao_vu')->prefix('thong-bao')->name('announcements.')->group(function () {
        Route::get('/', [ThongBaoController::class, 'index'])->name('index');
        Route::get('/create', [ThongBaoController::class, 'create'])->name('create');
        Route::post('/', [ThongBaoController::class, 'store'])->name('store');
        Route::delete('/{announcement}', [ThongBaoController::class, 'destroy'])->whereNumber('announcement')->name('destroy');
    });

    Route::prefix('hop-thu')->name('inbox.')->group(function () {
        Route::get('/', [HopThuController::class, 'index'])->name('index');
        Route::post('/danh-dau-da-doc', [HopThuController::class, 'markAll'])->name('mark-all');
        Route::get('/{announcement}', [HopThuController::class, 'show'])->whereNumber('announcement')->name('show');
    });
    Route::get('/sinh-vien/bang-diem', [CongSinhVienController::class, 'grades'])
        ->middleware('role:sinh_vien')
        ->name('portal.grades');

    Route::get('/bao-cao/bang-diem/{student}', [BaoCaoController::class, 'transcript'])
        ->whereNumber('student')
        ->name('reports.transcript');
    Route::get('/bao-cao/lop-hoc-phan/{course}', [BaoCaoController::class, 'grades'])
        ->whereNumber('course')
        ->name('reports.grades');
});
