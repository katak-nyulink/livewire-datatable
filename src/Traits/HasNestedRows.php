<?php

namespace KatakNyulink\LivewireDatatable\Traits;

trait HasNestedRows
{
    public array $expandedRows = [];
    public array $nestedData = [];
    public array $nestedColumns = [];
    public array $nestedRelations = [];
    public array $nestedConfig = [];

    public function initNestedRows(): void
    {
        $persist = config('datatable.persist_state', true);
        $prefix = config('datatable.session_key', 'datatable');

        if (empty($this->expandedRows)) {
            $this->expandedRows = $persist ? session("{$prefix}.expandedRows", []) : [];
        }
        // $this->nestedData = [];
    }

    public function registerNested(string $relation, array $config): void
    {
        if (! in_array($relation, $this->nestedRelations, true)) {
            $this->nestedRelations[] = $relation;
        }
        $this->nestedConfig[$relation] = $config;
        $this->nestedColumns[$relation] = $config['columns'] ?? [];
    }

    public function toggleRow(string|int $rowId, string $relation): void
    {
        $key = "{$relation}:{$rowId}";

        if (in_array($key, $this->expandedRows, true)) {
            $this->expandedRows = array_values(
                array_diff($this->expandedRows, [$key])
            );
            unset($this->nestedData[$key]);
        } else {
            $this->expandedRows[] = $key;
            $this->loadNestedChildren($rowId, $relation);
        }

        if (config('datatable.persist_state', true)) {
            $prefix = config('datatable.session_key', 'datatable');
            session(["{$prefix}.expandedRows" => $this->expandedRows]);
        }
    }

    public function isExpanded(string|int $rowId, string $relation): bool
    {
        return in_array("{$relation}:{$rowId}", $this->expandedRows, true);
    }

    protected function loadNestedChildren(string|int $rowId, string $relation): void
    {
        $key = "{$relation}:{$rowId}";
        $config = $this->nestedConfig[$relation] ?? [];

        $parent = $this->model::find($rowId);

        if (! $parent) {
            $this->nestedData[$key] = collect();
            return;
        }

        $query = $parent->{$relation}();

        if (! empty($config['with'])) {
            $query->with($config['with']);
        }

        if (! empty($config['orderBy'])) {
            [$col, $dir] = $config['orderBy'];
            $query->orderBy($col, $dir);
        }

        $this->nestedData[$key] = $query->get();
    }

    public function getNestedChildren(string|int $rowId, string $relation)
    {
        return $this->nestedData["{$relation}:{$rowId}"] ?? collect();
    }

    public function getHasNestedProperty(): bool
    {
        return ! empty($this->nestedRelations);
    }
}