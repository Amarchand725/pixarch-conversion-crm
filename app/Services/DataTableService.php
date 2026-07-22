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

            $searchable = $opts['searchable'] ?? false;

            return [
                'data'       => $key,
                'name'       => is_string($searchable) ? $searchable : $key,
                'orderable'  => $opts['orderable'] ?? true,
                'searchable' => $searchable !== false,
            ];
        })->values()->toArray();
    }

    // public function jsColumns()
    // {
    //     return collect($this->columns)->map(function ($opts, $key) {
    //         return [
    //             'data'       => $key,
    //             'name'       => $opts['searchable'] && is_string($opts['searchable']) 
    //                             ? $opts['searchable'] 
    //                             : $key,
    //             'orderable'  => $opts['orderable'] ?? true,
    //             'searchable' => !empty($opts['searchable']), // only true if searchable is set
    //         ];
    //     })->values()->toArray();
    // }

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
            // 🔥 Format created_at manually
            if ($key === 'created_at') {
                $dt->editColumn('created_at', function ($row) {
                    return $row->created_at
                        ? $row->created_at->format('d M Y | h:i A')
                        : null;
                });
                continue;
            }
            
            // if (!empty($meta['db'])) {
            //     $dt->editColumn($key, fn($row) => $row->$key);
            // } elseif (!empty($meta['html'])) {
            //     $dt->addColumn($key, fn($row) => $this->rowFormatter ? ($this->rowFormatter)($row)->$key : '');
            // } else {
            //     $dt->addColumn($key, fn($row) => $row->$key ?? null);
            // }

            // Cache formatted rows so formatRow() runs only once per record
            static $formattedRows = [];

            $callback = function ($row) use ($key, &$formattedRows) {

                $id = $row->id;

                if (!isset($formattedRows[$id])) {
                    $formattedRows[$id] = $this->rowFormatter
                        ? ($this->rowFormatter)(clone $row)
                        : clone $row;
                }

                return $formattedRows[$id]->{$key} ?? null;
            };

            if (!empty($meta['db'])) {
                $dt->editColumn($key, $callback);
            } elseif (!empty($meta['html'])) {

                // Existing DB column or custom column?
                if (array_key_exists($key, $this->model->getModel()->getAttributes())) {
                    $dt->editColumn($key, $callback);
                } else {
                    $dt->addColumn($key, $callback);
                }

            } else {
                $dt->addColumn($key, fn($row) => $row->{$key} ?? null);
            }

            // Generic filter/search — move inside the loop!
            if (!empty($meta['searchable']) && $meta['searchable'] !== false) {
                $dt->filterColumn($key, function($query, $keyword) use ($meta) {
                    if (str_contains($meta['searchable'], '.')) {
                        // relation search: 'relation.column'
                        [$relation, $column] = explode('.', $meta['searchable']);
                        $query->whereHas($relation, function($q) use ($keyword, $column) {
                            $q->where($column, 'like', "%{$keyword}%");
                        });
                    } else {
                        // normal DB column
                        $query->where($meta['searchable'], 'like', "%{$keyword}%");
                    }
                });
            }
        }
        
        return $dt->rawColumns($this->htmlColumns())->make(true);
    }
}
