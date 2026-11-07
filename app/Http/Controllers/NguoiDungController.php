<?php

namespace App\Http\Controllers;

use App\Enums\VaiTro;
use App\Models\NguoiDung;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NguoiDungController extends Controller
{
    public function index(Request $request): View
    {
        $users = NguoiDung::query()
            ->when($request->filled('q'), function ($query) use ($request): void {
                $keyword = trim((string) $request->input('q'));
                $query->where(fn ($query) => $query
                    ->where('ho_ten', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%"));
            })
            ->when($request->filled('vai_tro'), fn ($query) => $query->where('vai_tro', $request->input('vai_tro')))
            ->when($request->filled('trang_thai'), fn ($query) => $query->where('trang_thai', $request->boolean('trang_thai')))
            ->orderBy('ho_ten')
            ->paginate(15)
            ->withQueryString();

        return view('quan-tri.nguoi-dung.danh-sach', compact('users'));
    }

    public function create(): View
    {
        return view('quan-tri.nguoi-dung.bieu-mau', ['user' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ho_ten' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:nguoi_dung,email'],
            'vai_tro' => ['required', Rule::enum(VaiTro::class)],
            'so_dien_thoai' => ['nullable', 'string', 'max:15'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], $this->messages());

        $data['trang_thai'] = $request->boolean('trang_thai', true);
        NguoiDung::create($data);

        return redirect()->route('users.index')->with('success', 'Đã tạo tài khoản.');
    }

    public function edit(string $id): View
    {
        return view('quan-tri.nguoi-dung.bieu-mau', ['user' => NguoiDung::findOrFail($id)]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $user = NguoiDung::findOrFail($id);

        $data = $request->validate([
            'ho_ten' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', Rule::unique('nguoi_dung', 'email')->ignore($user->id)],
            'vai_tro' => ['required', Rule::enum(VaiTro::class)],
            'so_dien_thoai' => ['nullable', 'string', 'max:15'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], $this->messages());

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        if ($user->id === $request->user()->id && $data['vai_tro'] !== $user->vai_tro->value) {
            return back()->with('error', 'Bạn không thể tự thay đổi vai trò của mình.')->withInput();
        }

        if ($user->id === $request->user()->id && ! $request->boolean('trang_thai')) {
            return back()->with('error', 'Bạn không thể tự khoá tài khoản của mình.')->withInput();
        }

        $data['trang_thai'] = $request->boolean('trang_thai');
        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Đã cập nhật tài khoản.');
    }

    public function destroy(Request $request, string $id): RedirectResponse
    {
        $user = NguoiDung::findOrFail($id);

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Bạn không thể xoá chính mình.');
        }

        if ($user->student()->exists()) {
            return back()->with('error', 'Không thể xoá tài khoản đã liên kết hồ sơ sinh viên.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Đã xoá tài khoản.');
    }

    public function resetPassword(Request $request, string $id): RedirectResponse
    {
        $user = NguoiDung::findOrFail($id);

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Hãy đổi mật khẩu ở trang hồ sơ cá nhân.');
        }

        $password = (string) config('qlsv.mat_khau_mac_dinh');
        $user->update(['password' => $password]);

        return back()->with('success', "Đã đặt lại mật khẩu cho {$user->email}!");
    }

    public function toggleStatus(Request $request, string $id): RedirectResponse
    {
        $user = NguoiDung::findOrFail($id);

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Bạn không thể tự khoá tài khoản của mình.');
        }

        $user->update(['trang_thai' => ! $user->trang_thai]);

        return back()->with('success', $user->trang_thai ? 'Đã mở khoá tài khoản.' : 'Đã khoá tài khoản.');
    }

    /**
     * @return array<string, string>
     */
    private function messages(): array
    {
        return [
            'ho_ten.required' => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.unique' => 'Email đã được sử dụng.',
            'role.required' => 'Vui lòng chọn vai trò.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ];
    }
}
