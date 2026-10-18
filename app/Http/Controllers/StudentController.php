<?php

namespace App\Http\Controllers;

use App\Enums\GioiTinh;
use App\Enums\Role;
use App\Enums\TrangThaiSinhVien;
use App\Models\Faculty;
use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Services\GpaService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $students = $this->filteredQuery($request)->paginate(15)->withQueryString();

        return view('students.index', [
            'students' => $students,
            'faculties' => Faculty::orderBy('ten_khoa')->pluck('ten_khoa', 'id'),
            'majors' => Major::orderBy('ten_nganh')->pluck('ten_nganh', 'id'),
            'classes' => SchoolClass::orderBy('ten_lop')->pluck('ten_lop', 'id'),
            'statuses' => TrangThaiSinhVien::options(),
        ]);
    }

    public function create(): View
    {
        return view('students.form', $this->formData(null));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules(null), $this->messages());

        if ($request->hasFile('anh')) {
            $data['anh'] = $this->storeImage($request, $data['ma_sv']);
        }

        $student = DB::transaction(function () use ($data, $request): Student {
            $student = Student::create($data);

            if ($request->boolean('tao_tai_khoan')) {
                $this->createAccount($student);
            }

            return $student;
        });

        $message = 'Đã thêm sinh viên '.$student->ho_ten.'.';

        if ($student->user) {
            $message .= ' Tài khoản: '.$student->user->email.' — mật khẩu: '.$this->defaultPassword();
        }

        return redirect()->route('students.index')->with('success', $message);
    }

    public function show(string $id, GpaService $gpa): View
    {
        $student = Student::with([
            'lop.major.faculty',
            'user',
            'enrollments.course.subject',
            'enrollments.course.semester',
            'enrollments.grade',
        ])->findOrFail($id);

        return view('students.show', [
            'student' => $student,
            'gpa' => $gpa->tinh($student),
        ]);
    }

    public function edit(string $id): View
    {
        return view('students.form', $this->formData(Student::findOrFail($id)));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $student = Student::findOrFail($id);
        $data = $request->validate($this->rules($student), $this->messages());

        if ($request->hasFile('anh')) {
            $this->deleteImage($student);
            $data['anh'] = $this->storeImage($request, $data['ma_sv']);
        }

        DB::transaction(function () use ($student, $data, $request): void {
            $student->update($data);

            if ($request->boolean('tao_tai_khoan') && ! $student->user) {
                $this->createAccount($student);
            }
        });

        return redirect()->route('students.index')->with('success', 'Đã cập nhật hồ sơ sinh viên.');
    }

    public function destroy(string $id): RedirectResponse
    {
        Student::findOrFail($id)->delete();

        return redirect()->route('students.index')->with('success', 'Đã xoá mềm sinh viên. Có thể khôi phục trong thùng rác.');
    }

    public function trash(): View
    {
        return view('students.trash', [
            'students' => Student::onlyTrashed()->with('lop')->orderByDesc('deleted_at')->paginate(15),
        ]);
    }

    public function restore(string $id): RedirectResponse
    {
        Student::onlyTrashed()->findOrFail($id)->restore();

        return redirect()->route('students.trash')->with('success', 'Đã khôi phục hồ sơ sinh viên.');
    }

    public function forceDelete(string $id): RedirectResponse
    {
        abort_unless(request()->user()->role === Role::ADMIN, 403);

        $student = Student::onlyTrashed()->findOrFail($id);
        $this->deleteImage($student);
        $student->forceDelete();

        return redirect()->route('students.trash')->with('success', 'Đã xoá vĩnh viễn hồ sơ sinh viên.');
    }

    public function importForm(): View
    {
        return view('students.import');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:5120'],
        ], ['file.required' => 'Vui lòng chọn file CSV.']);

        $handle = fopen($request->file('file')->getRealPath(), 'r');

        if ($handle === false) {
            return back()->with('error', 'Không đọc được file.');
        }

        $headers = fgetcsv($handle);
        $headers = array_map(fn ($value) => trim(str_replace("\xEF\xBB\xBF", '', (string) $value)), $headers ?: []);

        foreach (['ma_sv', 'ho_ten', 'ngay_sinh', 'gioi_tinh', 'ma_lop'] as $requiredHeader) {
            if (! in_array($requiredHeader, $headers, true)) {
                fclose($handle);

                return back()->with('error', "File thiếu cột {$requiredHeader}. Hãy tải file mẫu.");
            }
        }

        $classMap = SchoolClass::pluck('id', 'ma_lop');
        $errors = [];
        $success = 0;
        $rowNumber = 1;
        $seen = [];

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            if (count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0) {
                continue;
            }

            $row = array_pad($row, count($headers), null);
            $data = array_combine($headers, array_slice($row, 0, count($headers)));
            $maLop = trim((string) ($data['ma_lop'] ?? ''));

            if (! isset($classMap[$maLop])) {
                $errors[] = "Dòng {$rowNumber}: mã lớp {$maLop} không tồn tại.";

                continue;
            }

            if (isset($seen[$data['ma_sv']])) {
                $errors[] = "Dòng {$rowNumber}: mã sinh viên {$data['ma_sv']} trùng trong file.";

                continue;
            }

            $payload = [
                'ma_sv' => trim((string) ($data['ma_sv'] ?? '')),
                'ho_ten' => trim((string) ($data['ho_ten'] ?? '')),
                'ngay_sinh' => trim((string) ($data['ngay_sinh'] ?? '')),
                'gioi_tinh' => trim((string) ($data['gioi_tinh'] ?? 'nam')) ?: 'nam',
                'lop_id' => $classMap[$maLop],
                'email' => trim((string) ($data['email'] ?? '')) ?: null,
                'sdt' => trim((string) ($data['sdt'] ?? '')) ?: null,
                'cccd' => trim((string) ($data['cccd'] ?? '')) ?: null,
                'dia_chi' => trim((string) ($data['dia_chi'] ?? '')) ?: null,
                'trang_thai' => TrangThaiSinhVien::DANG_HOC->value,
            ];

            $validator = Validator::make($payload, [
                'ma_sv' => ['required', 'string', 'max:20', 'unique:students,ma_sv'],
                'ho_ten' => ['required', 'string', 'max:150'],
                'ngay_sinh' => ['required', 'date', 'before:today'],
                'gioi_tinh' => ['required', Rule::enum(GioiTinh::class)],
                'lop_id' => ['required', 'exists:classes,id'],
                'email' => ['nullable', 'email', 'max:150'],
                'sdt' => ['nullable', 'string', 'max:15'],
                'cccd' => ['nullable', 'string', 'max:20', 'unique:students,cccd'],
            ]);

            if ($validator->fails()) {
                $errors[] = "Dòng {$rowNumber}: ".implode(' ', $validator->errors()->all());

                continue;
            }

            Student::create($validator->validated());
            $seen[$payload['ma_sv']] = true;
            $success++;
        }

        fclose($handle);

        return redirect()->route('students.index')
            ->with('success', "Import hoàn tất: {$success} dòng thành công, ".count($errors).' dòng lỗi.')
            ->with('import_errors', $errors);
    }

    public function template(): StreamedResponse
    {
        return response()->streamDownload(function (): void {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['ma_sv', 'ho_ten', 'ngay_sinh', 'gioi_tinh', 'ma_lop', 'email', 'sdt', 'cccd', 'dia_chi']);
            fputcsv($out, ['SV016', 'Nguyễn Văn Mẫu', '2005-01-01', 'nam', 'CNTT-K23-A', 'mau.nguyen@qlsv.test', '0912000016', '', 'Hà Nội']);
            fclose($out);
        }, 'mau-import-sinh-vien.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function export(Request $request): StreamedResponse
    {
        $students = $this->filteredQuery($request)->get();

        return response()->streamDownload(function () use ($students): void {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Mã SV', 'Họ tên', 'Ngày sinh', 'Giới tính', 'Lớp', 'Ngành', 'Khoa', 'Email', 'SĐT', 'CCCD', 'Địa chỉ', 'Trạng thái']);

            foreach ($students as $student) {
                fputcsv($out, [
                    $student->ma_sv,
                    $student->ho_ten,
                    $student->ngay_sinh?->format('d/m/Y'),
                    $student->gioi_tinh->label(),
                    $student->lop?->ten_lop,
                    $student->lop?->major?->ten_nganh,
                    $student->lop?->major?->faculty?->ten_khoa,
                    $student->email,
                    $student->sdt,
                    $student->cccd,
                    $student->dia_chi,
                    $student->trang_thai->label(),
                ]);
            }

            fclose($out);
        }, 'danh-sach-sinh-vien-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(?Student $student): array
    {
        return [
            'student' => $student,
            'classes' => SchoolClass::with('major')->orderBy('ten_lop')->get(),
            'gioiTinhs' => GioiTinh::options(),
            'statuses' => TrangThaiSinhVien::options(),
        ];
    }

    /**
     * @return Builder<Student>
     */
    private function filteredQuery(Request $request)
    {
        return Student::query()
            ->with(['lop.major.faculty', 'user'])
            ->filter([
                'keyword' => $request->input('q'),
                'lop_id' => $request->input('lop_id'),
                'trang_thai' => $request->input('trang_thai'),
            ])
            ->when($request->filled('khoa_id'), fn ($query) => $query->whereHas(
                'lop.major',
                fn ($query) => $query->where('faculty_id', $request->input('khoa_id'))
            ))
            ->when($request->filled('nganh_id'), fn ($query) => $query->whereHas(
                'lop',
                fn ($query) => $query->where('major_id', $request->input('nganh_id'))
            ))
            ->when($request->filled('gioi_tinh'), fn ($query) => $query->where('gioi_tinh', $request->input('gioi_tinh')))
            ->orderBy('ma_sv');
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(?Student $student): array
    {
        return [
            'ma_sv' => ['required', 'string', 'max:20', Rule::unique('students', 'ma_sv')->ignore($student?->id)],
            'ho_ten' => ['required', 'string', 'max:150'],
            'ngay_sinh' => ['required', 'date', 'before:today'],
            'gioi_tinh' => ['required', Rule::enum(GioiTinh::class)],
            'lop_id' => ['required', 'exists:classes,id'],
            'email' => ['nullable', 'email', 'max:150'],
            'sdt' => ['nullable', 'string', 'max:15'],
            'cccd' => ['nullable', 'string', 'max:20', Rule::unique('students', 'cccd')->ignore($student?->id)],
            'dia_chi' => ['nullable', 'string', 'max:500'],
            'anh' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'trang_thai' => ['required', Rule::enum(TrangThaiSinhVien::class)],
            'ngay_nhap_hoc' => ['nullable', 'date'],
            'ghi_chu' => ['nullable', 'string', 'max:1000'],
            'tao_tai_khoan' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function messages(): array
    {
        return [
            'ma_sv.required' => 'Vui lòng nhập mã sinh viên.',
            'ma_sv.unique' => 'Mã sinh viên đã tồn tại (kể cả hồ sơ đã xoá mềm).',
            'ho_ten.required' => 'Vui lòng nhập họ tên.',
            'ngay_sinh.required' => 'Vui lòng chọn ngày sinh.',
            'ngay_sinh.before' => 'Ngày sinh phải trước hôm nay.',
            'lop_id.required' => 'Vui lòng chọn lớp.',
            'email.email' => 'Email không đúng định dạng.',
            'anh.image' => 'Ảnh đại diện phải là file ảnh.',
            'anh.max' => 'Ảnh đại diện tối đa 2 MB.',
        ];
    }

    private function storeImage(Request $request, string $maSv): string
    {
        $file = $request->file('anh');

        return $file->storeAs(
            'students',
            $maSv.'-'.now()->timestamp.'.'.$file->extension(),
            'public'
        );
    }

    private function deleteImage(Student $student): void
    {
        if ($student->anh) {
            Storage::disk('public')->delete($student->anh);
        }
    }

    private function createAccount(Student $student): void
    {
        if (! $student->email) {
            throw ValidationException::withMessages([
                'email' => 'Cần nhập email sinh viên để tạo tài khoản đăng nhập.',
            ]);
        }

        if (User::where('email', $student->email)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'Email này đã được dùng cho tài khoản khác.',
            ]);
        }

        $user = User::create([
            'name' => $student->ho_ten,
            'email' => $student->email,
            'password' => $this->defaultPassword(),
            'role' => Role::SINH_VIEN,
            'trang_thai' => true,
            'email_verified_at' => now(),
        ]);

        $student->update(['user_id' => $user->id]);
        $student->setRelation('user', $user);
    }

    private function defaultPassword(): string
    {
        return 'Qlsv@'.now()->format('dmY');
    }
}
