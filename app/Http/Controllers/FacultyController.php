<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class FacultyController extends MasterDataController
{
    protected function modelClass(): string
    {
        return Faculty::class;
    }

    protected function config(): array
    {
        return [
            'title' => 'Khoa',
            'subtitle' => 'Danh mục khoa của trường',
            'route' => 'danh-muc.khoa',
            'search' => ['ma_khoa', 'ten_khoa', 'truong_khoa', 'email'],
            'search_placeholder' => 'Mã khoa, tên khoa, trưởng khoa...',
            'order' => ['ma_khoa', 'asc'],
            'columns' => [
                ['label' => 'Mã khoa', 'key' => 'ma_khoa'],
                ['label' => 'Tên khoa', 'key' => 'ten_khoa'],
                ['label' => 'Trưởng khoa', 'key' => 'truong_khoa'],
                ['label' => 'Email', 'key' => 'email'],
            ],
            'fields' => [
                ['name' => 'ma_khoa', 'label' => 'Mã khoa', 'type' => 'text', 'required' => true, 'placeholder' => 'VD: CNTT'],
                ['name' => 'ten_khoa', 'label' => 'Tên khoa', 'type' => 'text', 'required' => true, 'placeholder' => 'VD: Công nghệ thông tin'],
                ['name' => 'truong_khoa', 'label' => 'Trưởng khoa', 'type' => 'text'],
                ['name' => 'dien_thoai', 'label' => 'Điện thoại', 'type' => 'text'],
                ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
                ['name' => 'dia_chi', 'label' => 'Địa chỉ', 'type' => 'textarea'],
            ],
        ];
    }

    protected function rules(?Model $record): array
    {
        return [
            'ma_khoa' => ['required', 'string', 'max:20', Rule::unique('faculties', 'ma_khoa')->ignore($record?->getKey())],
            'ten_khoa' => ['required', 'string', 'max:150'],
            'truong_khoa' => ['nullable', 'string', 'max:150'],
            'dien_thoai' => ['nullable', 'string', 'max:15'],
            'email' => ['nullable', 'email', 'max:150'],
            'dia_chi' => ['nullable', 'string', 'max:500'],
        ];
    }
}
