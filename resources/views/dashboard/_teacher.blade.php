@php($card = 'rounded-2xl border border-slate-200 bg-white p-5 shadow-sm')

<div class="grid gap-4 sm:grid-cols-3">
    <div class="{{ $card }}"><p class="text-xs uppercase tracking-wide text-slate-500">Lớp đang dạy</p><p class="mt-2 text-3xl font-bold text-blue-700">{{ $data['courses']->count() }}</p></div>
    <div class="{{ $card }}"><p class="text-xs uppercase tracking-wide text-slate-500">Tổng sinh viên</p><p class="mt-2 text-3xl font-bold text-emerald-600">{{ $data['courses']->sum('enrollments_count') }}</p></div>
    <div class="{{ $card }}"><p class="text-xs uppercase tracking-wide text-slate-500">Lớp chưa nhập điểm</p><p class="mt-2 text-3xl font-bold text-amber-600">{{ $data['chuaNhap'] }}</p></div>
</div>

<div class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-200 px-5 py-4"><h2 class="font-semibold">Lớp học phần của tôi</h2></div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500"><tr><th class="px-5 py-3">Mã lớp HP</th><th class="px-5 py-3">Môn học</th><th class="px-5 py-3">Học kỳ</th><th class="px-5 py-3">Sĩ số</th><th class="px-5 py-3">Trạng thái</th><th class="px-5 py-3"></th></tr></thead>
            <tbody class="divide-y divide-slate-100">
            @forelse ($data['courses'] as $course)
                <tr>
                    <td class="px-5 py-3 font-medium">{{ $course->ma_lhp }}</td>
                    <td class="px-5 py-3">{{ $course->subject->ten_mh }}</td>
                    <td class="px-5 py-3">{{ $course->semester->ten_hoc_ky }} · {{ $course->semester->nam_hoc }}</td>
                    <td class="px-5 py-3">{{ $course->enrollments_count }}</td>
                    <td class="px-5 py-3">{{ $course->trang_thai->label() }}</td>
                    <td class="px-5 py-3 text-right"><a href="{{ route('courses.grades.edit', $course) }}" class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white">Nhập điểm</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-8 text-center text-slate-500">Chưa được phân công lớp học phần.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
