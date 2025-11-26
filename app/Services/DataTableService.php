<?php
namespace App\Services;

use Yajra\DataTables\Facades\DataTables;

class DataTableService
{
    public function __construct(
        public $model,
        public array $columns,
        public $rowFormatter = null
    ) {}

    public function headers()
    {
        return collect($this->columns)->map(fn($c) => $c['label'])->toArray();
    }

    public function jsColumns()
    {
        return collect($this->columns)->map(function ($opts, $key) {
            return [
                'data'       => $key,
                'name'       => $key,
                'orderable'  => $opts['orderable'] ?? true,
                'searchable' => $opts['searchable'] ?? true,
            ];
        })->values()->toArray();
    }

    public function htmlColumns()
    {
        return collect($this->columns)
            ->filter(fn($c) => !empty($c['html']))
            ->keys()
            ->toArray();
    }

    public function ajax()
    {
        $dt = DataTables::of($this->model);

        foreach ($this->columns as $key => $meta) {
            if (!empty($meta['db'])) {
                $dt->editColumn($key, fn($row) => $row->$key);
            } elseif (!empty($meta['html'])) {
                // computed / HTML column: use rowFormatter for this column
                $dt->addColumn($key, fn($row) => $this->rowFormatter ? ($this->rowFormatter)($row)->$key : '');
            } else {
                // computed non-HTML column
                $dt->addColumn($key, fn($row) => $row->$key ?? null);
            }
        }

        return $dt->rawColumns($this->htmlColumns())->make(true);
    }
}
