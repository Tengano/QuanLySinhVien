<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Models\Major;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class MajorController extends MasterDataController
{
    protected function modelClass(): string
    {
        return Major::class;
    }

    protected function config(): array
    {
        $faculties = Faculty::query()->orderBy('ten_khoa')->pluck('ten_khoa', 'id')->all();

        return [
            'title' => 'Ngành',
            'subtitle' => 'Ngành đào tạo thuộc khoa',
            'route' => 'danh-muc.nganh',
            'search' => ['ma_nganh', 'ten_nganh'],
            'search_placeholder' => 'Mã hoặc tên ngành...',
            'with' => ['faculty'],
            'order' => ['ma_nganh', 'asc'],
            'filters' => [
                ['name' => 'faculty_id', 'label' => 'Khoa', 'options' => $faculties],
            ],
            'columns' => [
                ['label' => 'Mã ngành', 'key' => 'ma_nganh'],
                ['label' => 'Tên ngành', 'key' => 'ten_nganh'],
                ['label' => 'Khoa', 'key' => 'faculty.ten_khoa'],
                ['label' => 'Số năm', 'key' => 'so_nam_dao_tao'],
            ],
            'fields' => [
                ['name' => 'ma_nganh', 'label' => 'Mã ngành', 'type' => 'text', 'required' => true, 'placeholder' => 'VD: KTPM'],
                ['name' => 'ten_nganh', 'label' => 'Tên ngành', 'type' => 'text', 'required' => true],
                ['name' => 'faculty_id', 'label' => 'Khoa', 'type' => 'select', 'options' => $faculties, 'required' => true],
                ['name' => 'so_nam_dao_tao', 'label' => 'Số năm đào tạo', 'type' => 'number', 'min' => 1, 'max' => 6, 'default' => 4, 'required' => true],
            ],
        ];
    }

    protected function rules(?Model $record): array
    {
        return [
            'faculty_id' => ['required', 'exists:faculties,id'],
            'ma_nganh' => ['required', 'string', 'max:20', Rule::unique('majors', 'ma_nganh')->ignore($record?->getKey())],
            'ten_nganh' => [
                'required', 'string', 'max:150',
                Rule::unique('majors', 'ten_nganh')
                    ->where(fn ($query) => $query->where('faculty_id', request('faculty_id')))
                    ->ignore($record?->getKey()),
            ],
            'so_nam_dao_tao' => ['required', 'integer', 'between:1,6'],
        ];
    }
}
