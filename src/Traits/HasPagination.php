<?php

namespace KatakNyulink\LivewireDatatable\Traits;

use Livewire\WithPagination;

trait HasPagination
{
    use WithPagination;

    public int $perPage = 10;

    public function initPagination(): void
    {
        $persist = config('datatable.persist_state', true);
        $prefix = config('datatable.session_key', 'datatable');

        $this->perPage = $persist
            ? session("{$prefix}.perPage", config('datatable.default_per_page', 10))
            : config('datatable.default_per_page', 10);
    }

    public function updatedPerPage(): void
    {
        if (config('datatable.persist_state', true)) {
            $prefix = config('datatable.session_key', 'datatable');
            session(["{$prefix}.perPage" => $this->perPage]);
        }

        $this->resetPage();
    }

    public function getPerPageOptionsProperty(): array
    {
        return config('datatable.per_page_options', [5, 10, 25, 50, 100]);
    }
}