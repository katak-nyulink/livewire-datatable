<?php

namespace KatakNyulink\LivewireDatatable\Traits;

trait HasSorting
{
    public string $sortField = '';
    public string $sortDirection = 'asc';

    public function initSorting(): void
    {
        $persist = config('datatable.persist_state', true);
        $prefix = config('datatable.session_key', 'datatable');

        $this->sortField = $persist ? session("{$prefix}.sortField", '') : '';
        $this->sortDirection = $persist ? session("{$prefix}.sortDirection", 'asc') : 'asc';
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $datatableConfig = config('datatable', []);

        if ($datatableConfig['persist_state'] ?? true) {
            $prefix = config('datatable.session_key', 'datatable');
            session([
                "{$prefix}.sortField" => $this->sortField,
                "{$prefix}.sortDirection" => $this->sortDirection,
            ]);
        }

        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }
    }

    public function applySorting($query)
    {
        if (! $this->sortField) return $query;

        // Skip sort untuk composite column
        $column = collect($this->columnDefinitions ?? [])
            ->firstWhere('key', $this->sortField);

        if ($column) {
            $arr = method_exists($column, 'toArray') ? $column->toArray() : [];
            if (!empty($arr['composite'])) return $query;
        }

        if (str_contains($this->sortField, ':')) {
            [, $column] = explode(':', $this->sortField, 2);
            return $query->orderBy($column, $this->sortDirection);
        }

        return $query->orderBy($this->sortField, $this->sortDirection);
    }

    public function clearSort(): void
    {
        $this->sortField = '';
        $this->sortDirection = 'asc';
    }
}
