<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HoSoCaNhanController extends Controller
{
    public function edit(Request $request): View
    {
        return view('ho-so.chinh-sua', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'ho_ten' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', Rule::unique('nguoi_dung', 'email')->ignore($user->id)],
            'so_dien_thoai' => ['nullable', 'string', 'max:15'],
            'anh_dai_dien' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ], [
            'ho_ten.required' => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.unique' => 'Email đã được sử dụng.',
            'anh_dai_dien.image' => 'Ảnh đại diện phải là file ảnh.',
            'anh_dai_dien.max' => 'Ảnh đại diện tối đa 2 MB.',
        ]);

        if ($request->hasFile('anh_dai_dien')) {
            if ($user->anh_dai_dien) {
                Storage::disk('public')->delete($user->anh_dai_dien);
            }

            $data['anh_dai_dien'] = $request->file('anh_dai_dien')->storeAs(
                'avatars',
                $user->id.'-'.now()->timestamp.'.'.$request->file('anh_dai_dien')->extension(),
                'public'
            );
        }

        $user->update($data);

        return back()->with('success', 'Đã cập nhật hồ sơ cá nhân.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.current_password' => 'Mật khẩu hiện tại không đúng.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
        ]);

        $request->user()->update(['password' => $data['password']]);

        return back()->with('success', 'Đã đổi mật khẩu.');
    }
}
