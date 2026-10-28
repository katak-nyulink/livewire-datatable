<?php

namespace KatakNyulink\LivewireDatatable\Traits;

trait HasSearch
{
    public string $search = '';

    public function initSearch(): void
    {
        $persist = config('datatable.persist_state', true);
        $prefix = config('datatable.session_key', 'datatable');

        $this->search = $persist ? session("{$prefix}.search", '') : '';
    }

    public function updatedSearch(): void
    {
        if (config('datatable.persist_state', true)) {
            $prefix = config('datatable.session_key', 'datatable');
            session(["{$prefix}.search" => $this->search]);
        }

        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }
    }

    public function clearSearch(): void
    {
        $this->search = '';
        $this->updatedSearch();
    }

    public function applySearch($query)
    {
        if (empty(trim($this->search))) return $query;

        $searchable = array_filter($this->columnDefinitions ?? [], function ($c) {
            if (! $c->searchable) return false;
            $arr = method_exists($c, 'toArray') ? $c->toArray() : [];
            return empty($arr['composite']);
        });

        if (empty($searchable)) return $query;

        $term = '%' . trim($this->search) . '%';

        $query->where(function ($q) use ($searchable, $term) {
            foreach ($searchable as $col) {
                if ($col->relation && str_contains($col->relation, ':')) {
                    [$relation, $column] = explode(':', $col->relation, 2);
                    $q->orWhereHas($relation, fn($s) => $s->where($column, 'like', $term));
                } else {
                    $q->orWhere($col->key, 'like', $term);
                }
            }
        });

        return $query;
    }
}
