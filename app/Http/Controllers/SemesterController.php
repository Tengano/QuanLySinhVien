<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class SemesterController extends MasterDataController
{
    protected function modelClass(): string
    {
        return Semester::class;
    }

    protected function config(): array
    {
        return [
            'title' => 'Học kỳ',
            'subtitle' => 'Học kỳ và năm học',
            'route' => 'danh-muc.hoc-ky',
            'search' => ['ten_hoc_ky', 'nam_hoc'],
            'search_placeholder' => 'Tên học kỳ hoặc năm học...',
            'order' => ['nam_hoc', 'desc'],
            'columns' => [
                ['label' => 'Học kỳ', 'key' => 'ten_hoc_ky'],
                ['label' => 'Số kỳ', 'key' => 'hoc_ky_so'],
                ['label' => 'Năm học', 'key' => 'nam_hoc'],
                ['label' => 'Ngày bắt đầu', 'key' => 'ngay_bat_dau', 'type' => 'date'],
                ['label' => 'Ngày kết thúc', 'key' => 'ngay_ket_thuc', 'type' => 'date'],
            ],
            'fields' => [
                ['name' => 'ten_hoc_ky', 'label' => 'Tên học kỳ', 'type' => 'select', 'options' => ['HK1' => 'HK1', 'HK2' => 'HK2', 'HK Hè' => 'HK Hè'], 'required' => true, 'empty' => false],
                ['name' => 'hoc_ky_so', 'label' => 'Số học kỳ', 'type' => 'select', 'options' => [1 => 'Học kỳ 1', 2 => 'Học kỳ 2', 3 => 'Học kỳ hè'], 'required' => true, 'empty' => false],
                ['name' => 'nam_hoc', 'label' => 'Năm học', 'type' => 'text', 'required' => true, 'placeholder' => 'VD: 2026-2027'],
                ['name' => 'ngay_bat_dau', 'label' => 'Ngày bắt đầu', 'type' => 'date', 'required' => true],
                ['name' => 'ngay_ket_thuc', 'label' => 'Ngày kết thúc', 'type' => 'date', 'required' => true],
            ],
        ];
    }

    protected function rules(?Model $record): array
    {
        return [
            'ten_hoc_ky' => ['required', Rule::in(['HK1', 'HK2', 'HK Hè'])],
            'hoc_ky_so' => [
                'required', 'integer', Rule::in([1, 2, 3]),
                Rule::unique('semesters', 'hoc_ky_so')
                    ->where(fn ($query) => $query->where('nam_hoc', request('nam_hoc')))
                    ->ignore($record?->getKey()),
            ],
            'nam_hoc' => ['required', 'string', 'regex:/^\d{4}-\d{4}$/'],
            'ngay_bat_dau' => ['required', 'date'],
            'ngay_ket_thuc' => ['required', 'date', 'after:ngay_bat_dau'],
        ];
    }
}
