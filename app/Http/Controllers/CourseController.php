<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Enums\TrangThaiLopHocPhan;
use App\Models\Course;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $courses = Course::query()
            ->with(['subject', 'semester', 'giangVien'])
            ->withCount('enrollments')
            ->when($user->role === Role::GIANG_VIEN, fn ($query) => $query->where('giang_vien_id', $user->id))
            ->when($request->filled('q'), function ($query) use ($request): void {
                $keyword = trim((string) $request->input('q'));
                $query->where(fn ($query) => $query
                    ->where('ma_lhp', 'like', "%{$keyword}%")
                    ->orWhereHas('subject', fn ($query) => $query->where('ten_mh', 'like', "%{$keyword}%")));
            })
            ->when($request->filled('semester_id'), fn ($query) => $query->where('semester_id', $request->input('semester_id')))
            ->when($request->filled('subject_id'), fn ($query) => $query->where('subject_id', $request->input('subject_id')))
            ->when($request->filled('trang_thai'), fn ($query) => $query->where('trang_thai', $request->input('trang_thai')))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('courses.index', [
            'courses' => $courses,
            'subjects' => Subject::orderBy('ten_mh')->pluck('ten_mh', 'id'),
            'semesters' => Semester::orderByDesc('nam_hoc')->get(),
            'statuses' => TrangThaiLopHocPhan::options(),
            'canCreate' => $user->role === Role::ADMIN || $user->role === Role::GIAO_VU,
        ]);
    }

    public function create(): View
    {
        return view('courses.form', $this->formData(null));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules(null), $this->messages());
        Course::create($data);

        return redirect()->route('courses.index')->with('success', 'Đã mở lớp học phần.');
    }

    public function show(Request $request, string $id): View
    {
        $course = Course::with([
            'subject',
            'semester',
            'giangVien',
            'enrollments.student.lop',
            'enrollments.grade',
        ])->findOrFail($id);

        $this->authorize('view', $course);

        $user = $request->user();

        return view('courses.show', [
            'course' => $course,
            'canEnroll' => $user->role === Role::ADMIN || $user->role === Role::GIAO_VU,
            'canGrade' => $user->role === Role::ADMIN || $user->role === Role::GIAO_VU
                || ($user->role === Role::GIANG_VIEN && $course->giang_vien_id === $user->id),
        ]);
    }

    public function edit(string $id): View
    {
        return view('courses.form', $this->formData(Course::findOrFail($id)));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $course = Course::findOrFail($id);
        $course->update($request->validate($this->rules($course), $this->messages()));

        return redirect()->route('courses.index')->with('success', 'Đã cập nhật lớp học phần.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $course = Course::findOrFail($id);

        if ($course->enrollments()->exists()) {
            return back()->with('error', 'Không thể xoá lớp HP đã có sinh viên ghi danh.');
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
    private function formData(?Course $course): array
    {
        return [
            'course' => $course,
            'subjects' => Subject::orderBy('ten_mh')->pluck('ten_mh', 'id'),
            'semesters' => Semester::orderByDesc('nam_hoc')->get(),
            'teachers' => User::where('role', Role::GIANG_VIEN->value)->orderBy('name')->pluck('name', 'id'),
            'statuses' => TrangThaiLopHocPhan::options(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(?Course $course): array
    {
        return [
            'ma_lhp' => ['required', 'string', 'max:30', Rule::unique('courses', 'ma_lhp')->ignore($course?->id)],
            'subject_id' => ['required', 'exists:subjects,id'],
            'semester_id' => ['required', 'exists:semesters,id'],
            'giang_vien_id' => ['nullable', Rule::exists('users', 'id')->where('role', Role::GIANG_VIEN->value)],
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
            'ma_lhp.required' => 'Vui lòng nhập mã lớp HP.',
            'ma_lhp.unique' => 'Mã lớp HP đã tồn tại.',
            'subject_id.required' => 'Vui lòng chọn môn học.',
            'semester_id.required' => 'Vui lòng chọn học kỳ.',
            'so_sv_toi_da.required' => 'Vui lòng nhập sĩ số tối đa.',
        ];
    }
}
