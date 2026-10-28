<div class="w-full space-y-4">
    @include('datatable::livewire.partials.toolbar')

    <flux:table :paginate="$data">
        <flux:table.columns>
            @if($this->hasNested)
                <flux:table.column class="w-10" />
            @endif

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
                        :class="$col->headClass"
                        wire:click="sortBy('{{ $col->key }}')"
                        class="{{ $col->widthClass() }}"
                        style="{{ $col->widthStyle() }}"
                    >
                        {{ $col->label }}
                    </flux:table.column>
                @else
                    <flux:table.column
                        :align="$col->align"
                        :class="$col->headClass"
                        class="{{ $col->widthClass() }}"
                        style="{{ $col->widthStyle() }}"
                    >
                        {{ $col->label }}
                    </flux:table.column>
                @endif
            @endforeach

            {{-- Kolom Actions --}}
            @if($this->hasRecordActions)
                <flux:table.column align="end" class="w-px">Aksi</flux:table.column>
            @endif
        </flux:table.columns>

        <flux:table.rows>
            @forelse($data as $row)
                <flux:table.row :key="$row->id">
                    @if($this->hasNested)
                        <flux:table.cell class="w-10">
                            @foreach($this->nestedRelations as $relation)
                                @php $isExpanded = $this->isExpanded($row->id, $relation); @endphp
                                <flux:button
                                    variant="ghost"
                                    size="sm"
                                    icon="chevron-right"
                                    class="transition-transform {{ $isExpanded ? 'rotate-90' : '' }}"
                                    wire:click="toggleRow({{ $row->id }}, '{{ $relation }}')"
                                />
                            @endforeach
                        </flux:table.cell>
                    @endif

                    @foreach($this->visibleColumnObjects as $col)
                        <flux:table.cell :align="$col->align" :variant="$col->variant">{!! $col->resolveValue($row) !!}</flux:table.cell>
                    @endforeach

                    {{-- Cell Actions --}}
                    @if($this->hasRecordActions)
                        <flux:table.cell align="end" class="py-0">
                            @include('datatable::livewire.partials.record-actions', ['row' => $row])
                        </flux:table.cell>
                    @endif
                </flux:table.row>

                @include('datatable::livewire.partials.nested-rows', ['row' => $row])
            @empty
                <flux:table.row>
                    <flux:table.cell align="center"
                        colspan="{{ count($this->visibleColumnObjects) + ($this->hasNested ? 1 : 0) + ($this->hasRecordActions ? 1 : 0) }}"
                        class="py-12 text-zinc-500">
                        {{ __('No records found.') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    {{-- Modal konfirmasi --}}
    @include('datatable::livewire.partials.action-confirm-modal')
</div>