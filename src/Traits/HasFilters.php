<?php

namespace KatakNyulink\LivewireDatatable\Traits;

trait HasFilters
{
    public array $filters = [];

    public function initFilters(): void
    {
        $persist = config('datatable.persist_state', true);
        $prefix = config('datatable.session_key', 'datatable');

        $this->filters = $persist ? session("{$prefix}.filters", []) : [];
    }

    public function updatedFilters(): void
    {
        if (config('datatable.persist_state', true)) {
            $prefix = config('datatable.session_key', 'datatable');
            session(["{$prefix}.filters" => $this->filters]);
        }

        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }
    }

    public function setFilter(string $key, mixed $value): void
    {
        $this->filters[$key] = $value;
        $this->updatedFilters();
    }

    public function clearFilters(): void
    {
        $this->filters = [];
        $this->updatedFilters();
    }

    public function applyFilters($query)
    {
        foreach ($this->filters as $key => $value) {
            if ($value === '' || $value === null || $value === []) {
                continue;
            }

            $column = collect($this->columnObjects ?? [])->firstWhere('key', $key);

            if (! $column || empty($column->meta['filterType'])) {
                $query->where($key, $value);
                continue;
            }

            match ($column->meta['filterType']) {
                'select'      => $query->where($key, $value),
                'boolean'     => $query->where($key, (bool) $value),
                'date_range'  => $query->whereBetween($key, $value),
                'number_range'=> $query->whereBetween($key, $value),
                'relation'    => $query->whereHas(
                    $column->meta['relation'],
                    fn ($q) => $q->where($column->meta['relationColumn'] ?? 'id', $value)
                ),
                default       => $query->where($key, $value),
            };
        }

        return $query;
    }
}