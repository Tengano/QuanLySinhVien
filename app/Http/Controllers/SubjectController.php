<?php

namespace App\Http\Controllers;

use App\Enums\LoaiMonHoc;
use App\Models\Faculty;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class SubjectController extends MasterDataController
{
    protected function modelClass(): string
    {
        return Subject::class;
    }

    protected function config(): array
    {
        $faculties = Faculty::query()->orderBy('ten_khoa')->pluck('ten_khoa', 'id')->all();

        return [
            'title' => 'Môn học',
            'subtitle' => 'Môn học, tín chỉ và loại môn',
            'route' => 'danh-muc.mon-hoc',
            'search' => ['ma_mh', 'ten_mh'],
            'search_placeholder' => 'Mã hoặc tên môn học...',
            'with' => ['faculty'],
            'order' => ['ma_mh', 'asc'],
            'filters' => [
                ['name' => 'faculty_id', 'label' => 'Khoa', 'options' => $faculties],
                ['name' => 'loai_mh', 'label' => 'Loại môn', 'options' => LoaiMonHoc::options()],
            ],
            'columns' => [
                ['label' => 'Mã môn', 'key' => 'ma_mh'],
                ['label' => 'Tên môn', 'key' => 'ten_mh'],
                ['label' => 'Khoa', 'key' => 'faculty.ten_khoa'],
                ['label' => 'Tín chỉ', 'key' => 'so_tin_chi'],
                ['label' => 'Loại môn', 'key' => 'loai_mh'],
            ],
            'fields' => [
                ['name' => 'ma_mh', 'label' => 'Mã môn', 'type' => 'text', 'required' => true, 'placeholder' => 'VD: LTW'],
                ['name' => 'ten_mh', 'label' => 'Tên môn', 'type' => 'text', 'required' => true],
                ['name' => 'faculty_id', 'label' => 'Khoa', 'type' => 'select', 'options' => $faculties],
                ['name' => 'so_tin_chi', 'label' => 'Số tín chỉ', 'type' => 'number', 'min' => 1, 'max' => 6, 'default' => 3, 'required' => true],
                ['name' => 'loai_mh', 'label' => 'Loại môn', 'type' => 'select', 'options' => LoaiMonHoc::options(), 'default' => LoaiMonHoc::BAT_BUOC->value, 'required' => true, 'empty' => false],
                ['name' => 'so_tiet_ly_thuyet', 'label' => 'Số tiết lý thuyết', 'type' => 'number', 'min' => 0, 'max' => 200],
                ['name' => 'so_tiet_thuc_hanh', 'label' => 'Số tiết thực hành', 'type' => 'number', 'min' => 0, 'max' => 200],
            ],
        ];
    }

    protected function rules(?Model $record): array
    {
        return [
            'ma_mh' => ['required', 'string', 'max:20', Rule::unique('subjects', 'ma_mh')->ignore($record?->getKey())],
            'ten_mh' => ['required', 'string', 'max:150'],
            'faculty_id' => ['nullable', 'exists:faculties,id'],
            'so_tin_chi' => ['required', 'integer', 'between:1,6'],
            'loai_mh' => ['required', Rule::enum(LoaiMonHoc::class)],
            'so_tiet_ly_thuyet' => ['nullable', 'integer', 'between:0,200'],
            'so_tiet_thuc_hanh' => ['nullable', 'integer', 'between:0,200'],
        ];
    }
}
