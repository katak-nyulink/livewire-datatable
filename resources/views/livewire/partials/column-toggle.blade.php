@php
    $hideableColumns = collect($this->columnObjects)->filter(fn($c) => $c->hideable);
@endphp

@if($hideableColumns->isNotEmpty())
    <flux:dropdown>
        <flux:button icon="view-columns" variant="ghost">{{ __('Columns') }}</flux:button>

        <flux:menu>
            <flux:menu.heading>{{ __('Show Columns') }}</flux:menu.heading>

            @foreach($hideableColumns as $col)
                <flux:menu.checkbox
                    wire:click="toggleColumn('{{ $col->key }}')"
                    :checked="in_array($col->key, $this->visibleColumns, true)"
                >
                    {{ $col->label }}
                </flux:menu.checkbox>
            @endforeach

            <flux:menu.separator />

            <flux:menu.item wire:click="resetColumns" icon="arrow-path">
                {{ __('Reset to default') }}
            </flux:menu.item>
        </flux:menu>
    </flux:dropdown>
@endif