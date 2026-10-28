<?php

namespace KatakNyulink\LivewireDatatable\Traits;

use Livewire\Attributes\Computed;
use KatakNyulink\LivewireDatatable\Support\Column;
use KatakNyulink\LivewireDatatable\Support\CompositeColumn;

trait HasColumns
{
    /** @var array<Column|CompositeColumn> — protected agar tidak diserialisasi Livewire */
    protected array $columnDefinitions = [];

    /** Public: user bisa toggle, primitif, aman */
    public array $visibleColumns = [];

    protected function columns(): array { return []; }

    public function addColumn(Column|CompositeColumn|string $column, ?callable $cb = null): static
    {
        if (is_string($column)) {
            $col = Column::make($column);
            if ($cb) $cb($col);
            $this->columnDefinitions[] = $col;
        } else {
            $this->columnDefinitions[] = $column;
        }
        return $this;
    }

    public function addColumns(array $columns): static
    {
        foreach ($columns as $col) $this->addColumn($col);
        return $this;
    }

    protected function bootColumns(): void
    {
        $this->columnDefinitions = []; // reset tiap request

        if (method_exists($this, 'columns')) {
            $this->columnDefinitions = array_merge($this->columnDefinitions, $this->columns());
        }
        $this->syncVisibleColumns();
    }

    protected function sessionKey(string $suffix): string
    {
        return config('datatable.session_key', 'datatable') . '.' . $suffix;
    }

    protected function syncVisibleColumns(): void
    {
        $persist = config('datatable.persist_state', true);
        $allKeys = array_map(fn ($c) => $c->key, $this->columnDefinitions);
        $hideableKeys = array_map(
            fn ($c) => $c->key,
            array_filter($this->columnDefinitions, fn ($c) => $c->hideable)
        );

        $this->visibleColumns = $persist ? session($this->sessionKey('visibleColumns'), []) : [];

        if (empty($this->visibleColumns)) {
            $this->visibleColumns = array_map(
                fn ($c) => $c->key,
                array_filter($this->columnDefinitions, fn ($c) => $c->visible)
            );
        } else {
            $forced = array_diff($allKeys, $hideableKeys);
            $this->visibleColumns = array_values(array_unique(array_merge($this->visibleColumns, $forced)));
        }
    }

    public function toggleColumn(string $key): void
    {
        if (in_array($key, $this->visibleColumns, true)) {
            $this->visibleColumns = array_values(array_diff($this->visibleColumns, [$key]));
        } else {
            $this->visibleColumns[] = $key;
        }

        if (config('datatable.persist_state', true)) {
            session([$this->sessionKey('visibleColumns') => $this->visibleColumns]);
        }
    }

    public function resetColumns(): void
    {
        $this->visibleColumns = array_map(
            fn ($c) => $c->key,
            array_filter($this->columnDefinitions, fn ($c) => $c->visible)
        );

        if (config('datatable.persist_state', true)) {
            session([$this->sessionKey('visibleColumns') => $this->visibleColumns]);
        }
    }

    /** Akses dari Blade: $this->columnObjects */
    #[Computed]
    public function columnObjects(): array
    {
        return $this->columnDefinitions;
    }

    /** Akses dari Blade: $this->visibleColumnObjects */
    #[Computed]
    public function visibleColumnObjects(): array
    {
        return array_values(array_filter(
            $this->columnDefinitions,
            fn ($c) => in_array($c->key, $this->visibleColumns, true)
        ));
    }

    /** Untuk dipakai trait lain (internal) */
    public function getColumnDefinitions(): array
    {
        return $this->columnDefinitions;
    }
}