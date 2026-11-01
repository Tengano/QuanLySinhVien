<?php

namespace App\Http\Controllers;

use App\Enums\VaiTro;
use App\Services\GpaService;
use App\Services\ThongKeService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BangDieuKhienController extends Controller
{
    public function index(Request $request, ThongKeService $thongKe, GpaService $gpa): View
    {
        $user = $request->user();

        if ($user->vai_tro === VaiTro::SINH_VIEN) {
            $student = $user->student()->with('lop.major.faculty')->first();
            $gpaData = $student ? $gpa->tinh($student, null, true) : null;

            return view('bang-dieu-khien.tong-quan', compact('user', 'student', 'gpaData'));
        }

        if ($user->vai_tro === VaiTro::GIANG_VIEN) {
            $data = $thongKe->giangVien($user);

            return view('bang-dieu-khien.tong-quan', compact('user', 'data'));
        }

        $data = $thongKe->admin();

        return view('bang-dieu-khien.tong-quan', compact('user', 'data'));
    }
}
