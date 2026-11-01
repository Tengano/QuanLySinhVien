<?php

namespace App\Http\Controllers;

use App\Enums\VaiTro;
use App\Enums\TrangThaiLopHocPhan;
use App\Models\LopHocPhan;
use App\Models\HocKy;
use App\Models\MonHoc;
use App\Models\NguoiDung;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LopHocPhanController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $courses = LopHocPhan::query()
            ->with(['subject', 'semester', 'giangVien'])
            ->withCount('enrollments')
            ->when($user->vai_tro === VaiTro::GIANG_VIEN, fn ($query) => $query->where('giang_vien_id', $user->id))
            ->when($request->filled('q'), function ($query) use ($request): void {
                $keyword = trim((string) $request->input('q'));
                $query->where(fn ($query) => $query
                    ->where('ma_lhp', 'like', "%{$keyword}%")
                    ->orWhereHas('subject', fn ($query) => $query->where('ten_mh', 'like', "%{$keyword}%")));
            })
            ->when($request->filled('hoc_ky_id'), fn ($query) => $query->where('hoc_ky_id', $request->input('hoc_ky_id')))
            ->when($request->filled('mon_hoc_id'), fn ($query) => $query->where('mon_hoc_id', $request->input('mon_hoc_id')))
            ->when($request->filled('trang_thai'), fn ($query) => $query->where('trang_thai', $request->input('trang_thai')))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('lop-hoc-phan.danh-sach', [
            'courses' => $courses,
            'subjects' => MonHoc::orderBy('ten_mh')->pluck('ten_mh', 'id'),
            'semesters' => HocKy::orderByDesc('nam_hoc')->get(),
            'statuses' => TrangThaiLopHocPhan::options(),
            'canCreate' => $user->vai_tro === VaiTro::ADMIN || $user->vai_tro === VaiTro::GIAO_VU,
        ]);
    }

    public function create(): View
    {
        return view('lop-hoc-phan.bieu-mau', $this->formData(null));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules(null), $this->messages());
        LopHocPhan::create($data);

        return redirect()->route('courses.index')->with('success', 'Đã mở lớp học phần.');
    }

    public function show(Request $request, string $id): View
    {
        $course = LopHocPhan::with([
            'subject',
            'semester',
            'giangVien',
            'enrollments.student.lop',
            'enrollments.grade',
        ])->findOrFail($id);

        $this->authorize('view', $course);

        $user = $request->user();

        return view('lop-hoc-phan.chi-tiet', [
            'course' => $course,
            'canEnroll' => $user->vai_tro === VaiTro::ADMIN || $user->vai_tro === VaiTro::GIAO_VU,
            'canGrade' => $user->vai_tro === VaiTro::ADMIN || $user->vai_tro === VaiTro::GIAO_VU
                || ($user->vai_tro === VaiTro::GIANG_VIEN && $course->giang_vien_id === $user->id),
        ]);
    }

    public function edit(string $id): View
    {
        return view('lop-hoc-phan.bieu-mau', $this->formData(LopHocPhan::findOrFail($id)));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $course = LopHocPhan::findOrFail($id);
        $course->update($request->validate($this->rules($course), $this->messages()));

        return redirect()->route('courses.index')->with('success', 'Đã cập nhật lớp học phần.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $course = LopHocPhan::findOrFail($id);

        if ($course->enrollments()->exists()) {
            return back()->with('error', 'Không thể xoá lớp học phần đã có sinh viên ghi danh.');
        }

        try {
            $course->delete();
        } catch (QueryException) {
            return back()->with('error', 'Không thể xoá lớp học phần vì đang có dữ liệu liên quan.');
        }

        return redirect()->route('courses.index')->with('success', 'Đã xoá lớp học phần.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(?LopHocPhan $course): array
    {
        return [
            'course' => $course,
            'subjects' => MonHoc::orderBy('ten_mh')->pluck('ten_mh', 'id'),
            'semesters' => HocKy::orderByDesc('nam_hoc')->get(),
            'teachers' => NguoiDung::where('vai_tro', VaiTro::GIANG_VIEN->value)->orderBy('ho_ten')->pluck('ho_ten', 'id'),
            'statuses' => TrangThaiLopHocPhan::options(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(?LopHocPhan $course): array
    {
        return [
            'ma_lhp' => ['required', 'string', 'max:30', Rule::unique('lop_hoc_phan', 'ma_lhp')->ignore($course?->id)],
            'mon_hoc_id' => ['required', 'exists:mon_hoc,id'],
            'hoc_ky_id' => ['required', 'exists:hoc_ky,id'],
            'giang_vien_id' => ['nullable', Rule::exists('nguoi_dung', 'id')->where('vai_tro', VaiTro::GIANG_VIEN->value)],
            'phong_hoc' => ['nullable', 'string', 'max:30'],
            'lich_hoc' => ['nullable', 'string', 'max:100'],
            'so_sv_toi_da' => ['required', 'integer', 'between:1,200'],
            'trang_thai' => ['required', Rule::enum(TrangThaiLopHocPhan::class)],
            'ghi_chu' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function messages(): array
    {
        return [
            'ma_lhp.required' => 'Vui lòng nhập mã lớp học phần.',
            'ma_lhp.unique' => 'Mã lớp học phần đã tồn tại.',
            'mon_hoc_id.required' => 'Vui lòng chọn môn học.',
            'hoc_ky_id.required' => 'Vui lòng chọn học kỳ.',
            'so_sv_toi_da.required' => 'Vui lòng nhập sĩ số tối đa.',
        ];
    }
}
