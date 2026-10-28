@php
    $filterableColumns = collect($this->columnObjects)
        ->filter(fn($c) => !empty($c->meta['filterType']));
@endphp

@if($filterableColumns->isNotEmpty())
    <flux:dropdown>
        <flux:button icon="funnel" variant="ghost">
            {{ __('Filter') }}
            @if(count(array_filter($filters ?? [])))
                <flux:badge size="sm" color="blue">{{ count(array_filter($filters ?? [])) }}</flux:badge>
            @endif
        </flux:button>

        <flux:menu class="w-72">
            @foreach($filterableColumns as $col)
                <div class="px-3 py-2">
                    <flux:text class="mb-1 text-xs font-medium text-zinc-600 dark:text-zinc-400">
                        {{ $col->label }}
                    </flux:text>

                    @if(($col->meta['filterType'] ?? '') === 'select')
                        <flux:select wire:model.live="filters.{{ $col->key }}">
                            <flux:select.option value="">{{ __('All') }}</flux:select.option>
                            @foreach(($col->meta['options'] ?? []) as $value => $label)
                                <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    @else
                        <flux:input
                            wire:model.live.debounce.400ms="filters.{{ $col->key }}"
                        />
                    @endif
                </div>
            @endforeach

            <flux:menu.separator />

            <flux:menu.item wire:click="clearFilters" icon="x-mark">
                {{ __('Reset filter') }}
            </flux:menu.item>
        </flux:menu>
    </flux:dropdown>
@endif