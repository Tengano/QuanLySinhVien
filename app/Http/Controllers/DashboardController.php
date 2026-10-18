<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Services\GpaService;
use App\Services\ThongKeService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, ThongKeService $thongKe, GpaService $gpa): View
    {
        $user = $request->user();

        if ($user->role === Role::SINH_VIEN) {
            $student = $user->student()->with('lop.major.faculty')->first();
            $gpaData = $student ? $gpa->tinh($student, null, true) : null;

            return view('dashboard.index', compact('user', 'student', 'gpaData'));
        }

        if ($user->role === Role::GIANG_VIEN) {
            $data = $thongKe->giangVien($user);

            return view('dashboard.index', compact('user', 'data'));
        }

        $data = $thongKe->admin();

        return view('dashboard.index', compact('user', 'data'));
    }
}
