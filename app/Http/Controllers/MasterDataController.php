<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

abstract class MasterDataController extends Controller
{
    /**
     * @return class-string<Model>
     */
    abstract protected function modelClass(): string;

    /**
     * @return array<string, mixed>
     */
    abstract protected function config(): array;

    /**
     * @return array<string, mixed>
     */
    abstract protected function rules(?Model $record): array;

    public function index(Request $request): View
    {
        $config = $this->config();
        $query = $this->modelClass()::query()->with($config['with'] ?? []);

        if ($keyword = trim((string) $request->input('q'))) {
            $query->where(function ($query) use ($config, $keyword): void {
                foreach ($config['search'] ?? [] as $column) {
                    $query->orWhere($column, 'like', "%{$keyword}%");
                }
            });
        }

        foreach ($config['filters'] ?? [] as $filter) {
            if ($request->filled($filter['name'])) {
                $query->where($filter['name'], $request->input($filter['name']));
            }
        }

        [$orderBy, $direction] = $config['order'] ?? ['id', 'desc'];

        $items = $query->orderBy($orderBy, $direction)->paginate(15)->withQueryString();

        return view('admin.crud.index', compact('config', 'items'));
    }

    public function create(): View
    {
        return view('admin.crud.form', [
            'config' => $this->config(),
            'record' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules(null), $this->messages(), $this->attributes());
        $this->modelClass()::create($data);

        return redirect()->route($this->config()['route'].'.index')
            ->with('success', 'Đã thêm mới '.$this->config()['title'].'.');
    }

    public function edit(string $id): View
    {
        return view('admin.crud.form', [
            'config' => $this->config(),
            'record' => $this->modelClass()::findOrFail($id),
        ]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $record = $this->modelClass()::findOrFail($id);
        $data = $request->validate($this->rules($record), $this->messages(), $this->attributes());
        $record->update($data);

        return redirect()->route($this->config()['route'].'.index')
            ->with('success', 'Đã cập nhật '.$this->config()['title'].'.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $record = $this->modelClass()::findOrFail($id);

        try {
            $record->delete();
        } catch (QueryException) {
            return back()->with('error', 'Không thể xoá vì dữ liệu này đang được sử dụng ở bảng khác.');
        }

        return redirect()->route($this->config()['route'].'.index')
            ->with('success', 'Đã xoá '.$this->config()['title'].'.');
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'required' => ':attribute là bắt buộc.',
            'unique' => ':attribute đã tồn tại.',
            'exists' => ':attribute không hợp lệ.',
            'numeric' => ':attribute phải là số.',
            'date' => ':attribute không đúng định dạng ngày.',
            'after' => ':attribute phải sau ngày bắt đầu.',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function attributes(): array
    {
        $attributes = [];

        foreach ($this->config()['fields'] ?? [] as $field) {
            $attributes[$field['name']] = $field['label'];
        }

        return $attributes;
    }
}
