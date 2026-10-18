<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class SchoolClassController extends MasterDataController
{
    protected function modelClass(): string
    {
        return SchoolClass::class;
    }

    protected function config(): array
    {
        $majors = Major::query()
            ->with('faculty')
            ->orderBy('ten_nganh')
            ->get()
            ->mapWithKeys(fn (Major $major): array => [$major->id => $major->ma_nganh.' — '.$major->ten_nganh])
            ->all();

        $teachers = User::query()
            ->where('role', Role::GIANG_VIEN->value)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();

        return [
            'title' => 'Lớp hành chính',
            'subtitle' => 'Lớp sinh hoạt theo ngành và niên khoá',
            'route' => 'danh-muc.lop',
            'search' => ['ma_lop', 'ten_lop'],
            'search_placeholder' => 'Mã hoặc tên lớp...',
            'with' => ['major.faculty', 'giaoVienChuNhiem'],
            'order' => ['ma_lop', 'asc'],
            'filters' => [
                ['name' => 'major_id', 'label' => 'Ngành', 'options' => $majors],
            ],
            'columns' => [
                ['label' => 'Mã lớp', 'key' => 'ma_lop'],
                ['label' => 'Tên lớp', 'key' => 'ten_lop'],
                ['label' => 'Ngành', 'key' => 'major.ten_nganh'],
                ['label' => 'Khoá', 'key' => 'khoa_hoc'],
                ['label' => 'GVCN', 'key' => 'giaoVienChuNhiem.name'],
            ],
            'fields' => [
                ['name' => 'ma_lop', 'label' => 'Mã lớp', 'type' => 'text', 'required' => true, 'placeholder' => 'VD: CNTT-K23-A'],
                ['name' => 'ten_lop', 'label' => 'Tên lớp', 'type' => 'text', 'required' => true],
                ['name' => 'major_id', 'label' => 'Ngành', 'type' => 'select', 'options' => $majors, 'required' => true],
                ['name' => 'khoa_hoc', 'label' => 'Niên khoá', 'type' => 'number', 'min' => 2000, 'max' => 2100, 'default' => 2023, 'required' => true],
                ['name' => 'gvcn_id', 'label' => 'Giáo viên chủ nhiệm', 'type' => 'select', 'options' => $teachers],
            ],
        ];
    }

    protected function rules(?Model $record): array
    {
        return [
            'major_id' => ['required', 'exists:majors,id'],
            'ma_lop' => ['required', 'string', 'max:30', Rule::unique('classes', 'ma_lop')->ignore($record?->getKey())],
            'ten_lop' => ['required', 'string', 'max:150'],
            'khoa_hoc' => ['required', 'integer', 'between:2000,2100'],
            'gvcn_id' => ['nullable', Rule::exists('users', 'id')->where('role', Role::GIANG_VIEN->value)],
        ];
    }
}
