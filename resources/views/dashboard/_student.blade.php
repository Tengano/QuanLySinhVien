@php($card = 'rounded-2xl border border-slate-200 bg-white p-5 shadow-sm')

@if (! $student)
    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
        Tài khoản của bạn chưa được liên kết với hồ sơ sinh viên. Vui lòng liên hệ giáo vụ.
    </div>
@else
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-xl font-semibold">{{ $student->ho_ten }}</p>
            <p class="text-sm text-slate-500">{{ $student->ma_sv }} · {{ $student->lop->ten_lop }} · {{ $student->lop->major->ten_nganh }}</p>
        </div>
        <a href="{{ route('portal.grades') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Xem bảng điểm</a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="{{ $card }}"><p class="text-xs uppercase tracking-wide text-slate-500">GPA tích luỹ</p><p class="mt-2 text-3xl font-bold text-blue-700">{{ number_format($gpaData['gpa'], 2) }}</p></div>
        <div class="{{ $card }}"><p class="text-xs uppercase tracking-wide text-slate-500">Tín chỉ đạt</p><p class="mt-2 text-3xl font-bold text-emerald-600">{{ $gpaData['tin_chi_dat'] }}<span class="text-base text-slate-400">/{{ $gpaData['tong_tin_chi'] }}</span></p></div>
        <div class="{{ $card }}"><p class="text-xs uppercase tracking-wide text-slate-500">Xếp loại</p><p class="mt-2 text-2xl font-bold">{{ $gpaData['xep_loai'] }}</p></div>
        <div class="{{ $card }}"><p class="text-xs uppercase tracking-wide text-slate-500">Môn đã có điểm</p><p class="mt-2 text-3xl font-bold">{{ count($gpaData['mon_hoc']) }}</p></div>
    </div>

    <div class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4"><h2 class="font-semibold">Kết quả học tập gần đây</h2></div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr><th class="px-5 py-3">Môn học</th><th class="px-5 py-3">Tín chỉ</th><th class="px-5 py-3">Điểm TB</th><th class="px-5 py-3">Điểm chữ</th><th class="px-5 py-3">Hệ 4</th><th class="px-5 py-3">Kết quả</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse ($gpaData['mon_hoc'] as $mon)
                    <tr>
                        <td class="px-5 py-3 font-medium">{{ $mon['ten_mh'] }}</td>
                        <td class="px-5 py-3">{{ $mon['so_tin_chi'] }}</td>
                        <td class="px-5 py-3">{{ number_format((float) $mon['diem_tb'], 2) }}</td>
                        <td class="px-5 py-3">{{ $mon['diem_chu'] }}</td>
                        <td class="px-5 py-3">{{ number_format($mon['diem_he4'], 2) }}</td>
                        <td class="px-5 py-3"><span class="rounded-full px-2 py-1 text-xs font-medium {{ $mon['dat'] ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">{{ $mon['dat'] ? 'Đạt' : 'Không đạt' }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-slate-500">Chưa có điểm đã chốt.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif
