<div class="flex flex-wrap items-center justify-between gap-2">
    <div class="flex flex-wrap items-center gap-2">
        {{-- Search --}}
        <flux:input
            wire:model.live.debounce.300ms="search"
            placeholder="{{ __('Search...') }}"
            icon="magnifying-glass"
            class="w-64"
        />

        {{-- Filter --}}
        @include('datatable::livewire.partials.filters')
    </div>

    {{-- Column Toggle --}}
    @include('datatable::livewire.partials.column-toggle')
</div>