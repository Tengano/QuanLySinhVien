<?php

namespace App\Http\Controllers;

use App\Enums\DoiTuongThongBao;
use App\Models\ThongBao;
use App\Models\LopHocPhan;
use App\Models\Khoa;
use App\Models\Lop;
use App\Models\NguoiDung;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ThongBaoController extends Controller
{
    public function index(): View
    {
        return view('thong-bao.danh-sach', [
            'announcements' => ThongBao::with('nguoiGui')
                ->withCount('users')
                ->orderByDesc('created_at')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('thong-bao.bieu-mau', [
            'faculties' => Khoa::orderBy('ten_khoa')->pluck('ten_khoa', 'id'),
            'classes' => Lop::orderBy('ten_lop')->pluck('ten_lop', 'id'),
            'courses' => LopHocPhan::with('subject')->orderByDesc('id')->get(),
            'users' => NguoiDung::orderBy('ho_ten')->pluck('ho_ten', 'id'),
            'targets' => DoiTuongThongBao::options(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'tieu_de' => ['required', 'string', 'max:200'],
            'noi_dung' => ['required', 'string', 'max:5000'],
            'doi_tuong' => ['required', Rule::enum(DoiTuongThongBao::class)],
            'khoa_id' => ['nullable', 'exists:khoa,id', Rule::requiredIf($request->input('doi_tuong') === DoiTuongThongBao::KHOA->value)],
            'lop_id' => ['nullable', 'exists:lop,id', Rule::requiredIf($request->input('doi_tuong') === DoiTuongThongBao::LOP->value)],
            'lop_hoc_phan_id' => ['nullable', 'exists:lop_hoc_phan,id', Rule::requiredIf($request->input('doi_tuong') === DoiTuongThongBao::LOP_HOC_PHAN->value)],
            'nguoi_nhan_id' => ['nullable', 'exists:nguoi_dung,id', Rule::requiredIf($request->input('doi_tuong') === DoiTuongThongBao::NGUOI_DUNG->value)],
        ], [
            'tieu_de.required' => 'Vui lòng nhập tiêu đề.',
            'noi_dung.required' => 'Vui lòng nhập nội dung.',
            'doi_tuong.required' => 'Vui lòng chọn đối tượng nhận.',
        ]);

        $recipients = $this->recipientIds($request);

        if ($recipients->isEmpty()) {
            return back()->withInput()->with('error', 'Không tìm thấy người nhận phù hợp với đối tượng đã chọn.');
        }

        $announcement = ThongBao::create([
            'tieu_de' => $request->input('tieu_de'),
            'noi_dung' => $request->input('noi_dung'),
            'nguoi_gui_id' => $request->user()->id,
            'doi_tuong' => $request->input('doi_tuong'),
            'khoa_id' => $request->input('khoa_id'),
            'lop_id' => $request->input('lop_id'),
            'lop_hoc_phan_id' => $request->input('lop_hoc_phan_id'),
            'nguoi_nhan_id' => $request->input('nguoi_nhan_id'),
        ]);

        $announcement->users()->syncWithoutDetaching(
            $recipients->mapWithKeys(fn (int $id): array => [$id => ['da_doc' => false]])->all()
        );

        return redirect()->route('announcements.index')
            ->with('success', 'Đã gửi thông báo cho '.$recipients->count().' người.');
    }

    public function destroy(string $id): RedirectResponse
    {
        ThongBao::findOrFail($id)->delete();

        return redirect()->route('announcements.index')->with('success', 'Đã xoá thông báo.');
    }

    /**
     * @return Collection<int, int>
     */
    private function recipientIds(Request $request)
    {
        $target = DoiTuongThongBao::from($request->input('doi_tuong'));

        return match ($target) {
            DoiTuongThongBao::TOAN_TRUONG => NguoiDung::where('trang_thai', true)->pluck('id'),
            DoiTuongThongBao::KHOA => NguoiDung::whereHas(
                'student.lop.major',
                fn ($query) => $query->where('khoa_id', $request->input('khoa_id'))
            )->pluck('id'),
            DoiTuongThongBao::LOP => NguoiDung::whereHas(
                'student',
                fn ($query) => $query->where('lop_id', $request->input('lop_id'))
            )->pluck('id'),
            DoiTuongThongBao::LOP_HOC_PHAN => NguoiDung::whereHas(
                'student.enrollments',
                fn ($query) => $query->where('lop_hoc_phan_id', $request->input('lop_hoc_phan_id'))
            )->pluck('id'),
            DoiTuongThongBao::NGUOI_DUNG => collect([(int) $request->input('nguoi_nhan_id')])->filter(),
        };
    }
}
