<?php

namespace KatakNyulink\LivewireDatatable\Livewire;

use Livewire\Component;
use KatakNyulink\LivewireDatatable\Traits\HasColumns;
use KatakNyulink\LivewireDatatable\Traits\HasFilters;
use KatakNyulink\LivewireDatatable\Traits\HasPagination;
use KatakNyulink\LivewireDatatable\Traits\HasSearch;
use KatakNyulink\LivewireDatatable\Traits\HasSorting;
use KatakNyulink\LivewireDatatable\Traits\HasRecordActions;

abstract class Datatable extends Component
{
    use HasColumns;
    use HasSorting;
    use HasSearch;
    use HasFilters;
    use HasRecordActions;
    use HasPagination;

    public string $model = '';
    public array $relations = [];

    public function mount(?string $model = null, array $relations = []): void
    {
        if ($model) {
            $this->model = $model;
        }

        if (empty($this->model)) {
            throw new \RuntimeException('DataTable requires a $model property or $model parameter.');
        }

        $this->relations = array_merge($this->relations, $relations);

        $this->initializeDataTable();
        $this->initializeTraits();
    }

    /**
     * Livewire memanggil hydrate() SETIAP request (setelah mount request pertama).
     * Di sini kita re-build semua non-serializable state.
     */
    public function hydrate(): void
    {
        $this->initializeDataTable();
    }

     /**
     * Rebuild semua state yang tidak diserialisasi Livewire (Column, Action, dsb).
     */
    protected function initializeDataTable(): void
    {
        $this->bootColumns();
        $this->bootActions();
    }

    protected function initializeTraits(): void
    {
        foreach (['Sorting', 'Search', 'Filters', 'Pagination'] as $trait) {
            $method = "init{$trait}";
            if (method_exists($this, $method)) {
                $this->{$method}();
            }
        }
    }

    protected function query(): \Illuminate\Database\Eloquent\Builder
    {
        $query = $this->model::query();

        if (! empty($this->relations)) {
            $query->with($this->relations);
        }

        if (method_exists($this, 'applyFilters')) {
            $query = $this->applyFilters($query);
        }
        if (method_exists($this, 'applySearch')) {
            $query = $this->applySearch($query);
        }
        if (method_exists($this, 'applySorting')) {
            $query = $this->applySorting($query);
        }

        return $query;
    }

    public function render()
    {
        return view('datatable::livewire.datatable', [
            'data' => $this->query()->paginate($this->perPage)
        ]);
    }
}