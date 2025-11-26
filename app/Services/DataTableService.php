<?php

namespace App\Services;

use Yajra\DataTables\Facades\DataTables;

class DataTableService
{
    protected $model;        // Eloquent model or query
    protected $columns = [];  // Column definitions
    protected $rawColumns = []; // Columns that contain HTML

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function setColumns(array $columns)
    {
        $this->columns = $columns;
        return $this;
    }

    public function setRawColumns(array $raw)
    {
        $this->rawColumns = $raw;
        return $this;
    }

    // Blade headers <th>
    public function getHeaders(): array
    {
        return array_map(fn($col) => $col['label'] ?? ucfirst($col), $this->columns);
    }

    // JS DataTable column definition
    public function getJsColumns(): array
    {
        return array_map(function($key, $col){
            return [
                'data' => $key,
                'name' => $key,
                'orderable' => $col['orderable'] ?? true,
                'searchable' => $col['searchable'] ?? true,
            ];
        }, array_keys($this->columns), $this->columns);
    }

    // Server-side processing
    public function handle($request, $rowCallback = null)
    {
        $query = $this->model;

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('*', function ($row) use ($rowCallback) {
                return $rowCallback ? $rowCallback($row) : $row;
            })
            ->rawColumns($this->rawColumns)
            ->make(true);
    }
}