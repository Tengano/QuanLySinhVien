@php($panel = 'rounded-2xl border border-slate-200 bg-white p-5 shadow-sm')

<div class="mt-6 grid gap-6 xl:grid-cols-2">
    <div class="{{ $panel }}">
        <h2 class="mb-4 font-semibold">Tỉ lệ Đạt / Không đạt theo môn</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-left text-xs uppercase text-slate-500"><tr><th class="pb-2">Môn học</th><th class="pb-2">Đạt</th><th class="pb-2">Không đạt</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                @forelse ($data['datRot'] as $row)
                    <tr><td class="py-2">{{ $row->ten_mh }}</td><td class="py-2 text-emerald-600">{{ $row->dat }}</td><td class="py-2 text-red-600">{{ $row->rot }}</td></tr>
                @empty
                    <tr><td colspan="3" class="py-4 text-center text-slate-500">Chưa có dữ liệu điểm.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="{{ $panel }}">
        <h2 class="mb-4 font-semibold">Điểm trung bình theo học kỳ</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-left text-xs uppercase text-slate-500"><tr><th class="pb-2">Học kỳ</th><th class="pb-2">Điểm TB</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                @forelse ($data['diemTheoKy'] as $row)
                    <tr><td class="py-2">{{ $row->ten_hoc_ky }} · {{ $row->nam_hoc }}</td><td class="py-2 font-semibold">{{ number_format((float) $row->trung_binh, 2) }}</td></tr>
                @empty
                    <tr><td colspan="2" class="py-4 text-center text-slate-500">Chưa có dữ liệu điểm.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
