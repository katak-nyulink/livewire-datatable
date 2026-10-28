<div class="w-full space-y-4">
    @include('datatable::livewire.partials.toolbar')

    <flux:table :paginate="$data">
        <flux:table.columns
            {{-- @if(config('my-datatable.sticky_header')) sticky @endif --}}
            :sticky="config('datatable.sticky_header')"
            @class([config('datatable.sticky_header') ? 'bg-white dark:bg-zinc-900' : ''])
            {{-- :style="config('datatable.sticky_header_max_height') ? 'max-height:' . config('datatable.sticky_header_max_height') : ''" --}}
        >
            @foreach($this->visibleColumnObjects as $col)
                @php
                    $widthAttrs = trim(($col->widthClass() ? 'class="' . $col->widthClass() . '"' : '')
                        . ' ' . ($col->widthStyle() ? 'style="' . $col->widthStyle() . '"' : ''));
                @endphp
                @if($col->sortable)
                    <flux:table.column
                        sortable
                        :sorted="$sortField === $col->key"
                        :direction="$sortDirection"
                        :align="$col->align"
                        :class="$col->resolvedHeadClass()"
                        :sticky="$col->sticky"
                        wire:click="sortBy('{{ $col->key }}')"
                        :style=" $col->widthStyle()"
                    >
                        {{ $col->label }}
                    </flux:table.column>
                @else
                    <flux:table.column
                        :sticky="$col->sticky"
                        :align="$col->align"
                        :class="$col->resolvedHeadClass()"
                        :style=" $col->widthStyle()"
                    >
                        {{ $col->label }}
                    </flux:table.column>
                @endif
            @endforeach

            {{-- KOLOM AKSI — render jika ada --}}
            @if($this->hasRecordActions)
                <flux:table.column align="end" class="w-px">{{ __('Actions') }}</flux:table.column>
            @endif
        </flux:table.columns>

        <flux:table.rows>
            @forelse($data as $row)
                <flux:table.row :key="$row->id">
                    @foreach($this->visibleColumnObjects as $col)
                        <flux:table.cell :variant="$col->variant" :align="$col->align" :sticky="$col->sticky" :class="$col->resolvedCellClass()">
                            {!! $col->resolveValue($row) !!}
                        </flux:table.cell>
                    @endforeach

                    {{-- CELL AKSI --}}
                    @if($this->hasRecordActions)
                        <flux:table.cell  align="end" class="py-1">
                            @include('datatable::livewire.partials.record-actions', ['row' => $row])
                        </flux:table.cell>
                    @endif
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell align="center"
                        colspan="{{ count($this->visibleColumnObjects) + ($this->hasRecordActions ? 1 : 0) }}"
                        class="py-12 text-zinc-500">
                        {{ __('No records found.') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    @include('datatable::livewire.partials.action-confirm-modal')
</div>