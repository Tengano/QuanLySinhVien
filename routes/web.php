<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check()
    ? redirect()->route('dashboard')
    : redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:login')->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Các route dưới đây sẽ được thay bằng controller thật ở các bước tiếp theo.
    Route::middleware('role:admin')->prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset');
        Route::patch('/{user}/toggle', [UserController::class, 'toggleStatus'])->name('toggle');
    });

    Route::middleware('role:admin,giao_vu')
        ->prefix('danh-muc')
        ->name('danh-muc.')
        ->group(function () {
            Route::resource('khoa', FacultyController::class)->except('show');
            Route::resource('nganh', MajorController::class)->except('show');
            Route::resource('lop', SchoolClassController::class)->except('show');
            Route::resource('mon-hoc', SubjectController::class)->except('show');
            Route::resource('hoc-ky', SemesterController::class)->except('show');
        });

    Route::middleware('role:admin,giao_vu')->prefix('sinh-vien')->name('students.')->group(function () {
        Route::get('/', [StudentController::class, 'index'])->name('index');
        Route::get('/create', [StudentController::class, 'create'])->name('create');
        Route::post('/', [StudentController::class, 'store'])->name('store');
        Route::get('/trash', [StudentController::class, 'trash'])->name('trash');
        Route::get('/import', [StudentController::class, 'importForm'])->name('import.form');
        Route::post('/import', [StudentController::class, 'import'])->name('import');
        Route::get('/template', [StudentController::class, 'template'])->name('template');
        Route::get('/export', [StudentController::class, 'export'])->name('export');
        Route::get('/{student}', [StudentController::class, 'show'])->whereNumber('student')->name('show');
        Route::get('/{student}/edit', [StudentController::class, 'edit'])->whereNumber('student')->name('edit');
        Route::put('/{student}', [StudentController::class, 'update'])->whereNumber('student')->name('update');
        Route::delete('/{student}', [StudentController::class, 'destroy'])->whereNumber('student')->name('destroy');
        Route::post('/{student}/restore', [StudentController::class, 'restore'])->whereNumber('student')->name('restore');
        Route::delete('/{student}/force', [StudentController::class, 'forceDelete'])->whereNumber('student')->name('force-delete');
    });

    Route::middleware('role:admin,giao_vu,giang_vien')->group(function () {
        Route::get('/lop-hoc-phan', [CourseController::class, 'index'])->name('courses.index');
        Route::get('/lop-hoc-phan/{course}', [CourseController::class, 'show'])->whereNumber('course')->name('courses.show');
        Route::get('/lop-hoc-phan/{course}/diem', [GradeController::class, 'edit'])->whereNumber('course')->name('courses.grades.edit');
        Route::put('/lop-hoc-phan/{course}/diem', [GradeController::class, 'update'])->whereNumber('course')->name('courses.grades.update');
        Route::post('/lop-hoc-phan/{course}/diem/chot', [GradeController::class, 'lock'])->whereNumber('course')->name('courses.grades.lock');
    });

    Route::middleware('role:admin,giao_vu')->group(function () {
        Route::get('/lop-hoc-phan/create', [CourseController::class, 'create'])->name('courses.create');
        Route::post('/lop-hoc-phan', [CourseController::class, 'store'])->name('courses.store');
        Route::get('/lop-hoc-phan/{course}/edit', [CourseController::class, 'edit'])->whereNumber('course')->name('courses.edit');
        Route::put('/lop-hoc-phan/{course}', [CourseController::class, 'update'])->whereNumber('course')->name('courses.update');
        Route::delete('/lop-hoc-phan/{course}', [CourseController::class, 'destroy'])->whereNumber('course')->name('courses.destroy');
        Route::post('/lop-hoc-phan/{course}/diem/mo-khoa', [GradeController::class, 'unlock'])->whereNumber('course')->name('courses.grades.unlock');
        Route::get('/lop-hoc-phan/{course}/ghi-danh', [EnrollmentController::class, 'create'])->whereNumber('course')->name('courses.enroll.create');
        Route::post('/lop-hoc-phan/{course}/ghi-danh', [EnrollmentController::class, 'store'])->whereNumber('course')->name('courses.enroll.store');
        Route::delete('/ghi-danh/{enrollment}', [EnrollmentController::class, 'destroy'])->whereNumber('enrollment')->name('enrollments.destroy');
    });

    Route::middleware('role:admin,giao_vu')->prefix('thong-bao')->name('announcements.')->group(function () {
        Route::get('/', [AnnouncementController::class, 'index'])->name('index');
        Route::get('/create', [AnnouncementController::class, 'create'])->name('create');
        Route::post('/', [AnnouncementController::class, 'store'])->name('store');
        Route::delete('/{announcement}', [AnnouncementController::class, 'destroy'])->whereNumber('announcement')->name('destroy');
    });

    Route::prefix('hop-thu')->name('inbox.')->group(function () {
        Route::get('/', [InboxController::class, 'index'])->name('index');
        Route::post('/danh-dau-da-doc', [InboxController::class, 'markAll'])->name('mark-all');
        Route::get('/{announcement}', [InboxController::class, 'show'])->whereNumber('announcement')->name('show');
    });
    Route::get('/sinh-vien/bang-diem', [PortalController::class, 'grades'])
        ->middleware('role:sinh_vien')
        ->name('portal.grades');

    Route::get('/bao-cao/bang-diem/{student}', [ReportController::class, 'transcript'])
        ->whereNumber('student')
        ->name('reports.transcript');
    Route::get('/bao-cao/lop-hoc-phan/{course}', [ReportController::class, 'grades'])
        ->whereNumber('course')
        ->name('reports.grades');
});
