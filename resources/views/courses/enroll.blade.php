@extends('layouts.app')

@section('title', 'Ghi danh sinh viên')
@section('subtitle', $course->ma_lhp.' · '.$course->subject->ten_mh)

@section('content')
    <div class="mb-5 flex items-center justify-between">
        <div>
            <a href="{{ route('courses.show', $course) }}" class="text-sm text-blue-700 hover:underline">← Chi tiết lớp HP</a>
            <p class="mt-1 text-sm text-slate-500">Sĩ số hiện tại: {{ $currentCount }}/{{ $course->so_sv_toi_da }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('courses.enroll.store', $course) }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        @csrf

        <div class="mb-3 flex items-center justify-between">
            <h2 class="font-semibold">Chọn sinh viên chưa ghi danh</h2>
            <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" id="check-all"> Chọn tất cả</label>
        </div>

        <div class="max-h-[500px] overflow-y-auto rounded-xl border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="sticky top-0 bg-slate-50 text-left text-xs uppercase text-slate-500"><tr><th class="px-4 py-3"></th><th class="px-4 py-3">Mã SV</th><th class="px-4 py-3">Họ tên</th><th class="px-4 py-3">Lớp</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                @forelse ($students as $student)
                    <tr>
                        <td class="px-4 py-2"><input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-checkbox"></td>
                        <td class="px-4 py-2 font-medium">{{ $student->ma_sv }}</td>
                        <td class="px-4 py-2">{{ $student->ho_ten }}</td>
                        <td class="px-4 py-2">{{ $student->lop?->ten_lop }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">Tất cả sinh viên đã được ghi danh.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5 flex justify-end gap-3">
            <a href="{{ route('courses.show', $course) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600">Huỷ</a>
            <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white">Ghi danh đã chọn</button>
        </div>
    </form>

    @push('scripts')
        <script>
            document.getElementById('check-all')?.addEventListener('change', (event) => {
                document.querySelectorAll('.student-checkbox').forEach((checkbox) => {
                    checkbox.checked = event.target.checked;
                });
            });
        </script>
    @endpush
@endsection
