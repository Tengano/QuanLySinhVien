@php($card = 'rounded-2xl border border-slate-200 bg-white p-5 shadow-sm')

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="{{ $card }}"><p class="text-xs uppercase tracking-wide text-slate-500">Sinh viên đang học</p><p class="mt-2 text-3xl font-bold text-blue-700">{{ $data['cards']['sinh_vien'] }}</p></div>
    <div class="{{ $card }}"><p class="text-xs uppercase tracking-wide text-slate-500">Lớp HP đang mở</p><p class="mt-2 text-3xl font-bold text-emerald-600">{{ $data['cards']['lop_hp'] }}</p></div>
    <div class="{{ $card }}"><p class="text-xs uppercase tracking-wide text-slate-500">Môn học</p><p class="mt-2 text-3xl font-bold text-violet-600">{{ $data['cards']['mon_hoc'] }}</p></div>
    <div class="{{ $card }}"><p class="text-xs uppercase tracking-wide text-slate-500">Giảng viên</p><p class="mt-2 text-3xl font-bold text-amber-600">{{ $data['cards']['giang_vien'] }}</p></div>
</div>

@php($maxKhoa = count($data['svTheoKhoa']) ? max($data['svTheoKhoa']) : 1)
@php($maxNganh = count($data['svTheoNganh']) ? max($data['svTheoNganh']) : 1)

<div class="mt-6 grid gap-6 xl:grid-cols-2">
    <div class="{{ $card }}">
        <h2 class="mb-4 font-semibold">Sinh viên theo Khoa</h2>
        @forelse ($data['svTheoKhoa'] as $ten => $soLuong)
            <div class="mb-3">
                <div class="mb-1 flex justify-between text-sm"><span>{{ $ten }}</span><span class="font-semibold">{{ $soLuong }}</span></div>
                <div class="h-2 rounded-full bg-slate-100"><div class="h-2 rounded-full bg-blue-600" style="width: {{ round($soLuong / $maxKhoa * 100) }}%"></div></div>
            </div>
        @empty
            <p class="text-sm text-slate-500">Chưa có dữ liệu.</p>
        @endforelse
    </div>

    <div class="{{ $card }}">
        <h2 class="mb-4 font-semibold">Sinh viên theo Ngành (top 6)</h2>
        @forelse ($data['svTheoNganh'] as $ten => $soLuong)
            <div class="mb-3">
                <div class="mb-1 flex justify-between text-sm"><span>{{ $ten }}</span><span class="font-semibold">{{ $soLuong }}</span></div>
                <div class="h-2 rounded-full bg-slate-100"><div class="h-2 rounded-full bg-emerald-500" style="width: {{ round($soLuong / $maxNganh * 100) }}%"></div></div>
            </div>
        @empty
            <p class="text-sm text-slate-500">Chưa có dữ liệu.</p>
        @endforelse
    </div>
</div>

@include('dashboard._admin-tables')
