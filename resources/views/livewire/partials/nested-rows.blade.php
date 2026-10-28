@foreach($this->nestedRelations as $relation)
    @php
        $isExpanded   = $this->isExpanded($row->id, $relation);
        $config       = $this->nestedConfig[$relation] ?? [];
        $children     = $isExpanded ? $this->getNestedChildren($row->id, $relation) : collect();
        $childColumns = $this->nestedColumns[$relation] ?? [];
    @endphp

    @if($isExpanded)
        <flux:table.row :key="'nested-' . $relation . '-' . $row->id">
            <flux:table.cell
                colspan="{{ count($this->visibleColumnObjects) + ($this->hasNested ? 1 : 0) + ($this->hasRecordActions ? 1 : 0) }}"
                class="p-0!"
            >
                <div class="border-l-4 border-blue-500 bg-zinc-50 px-6 py-4 dark:bg-zinc-900/60">
                    <flux:text class="mb-3 text-xs font-semibold uppercase tracking-wider text-zinc-500">
                        {{ $config['label'] ?? $relation }}
                    </flux:text>

                    <div
                        wire:loading
                        wire:target="toggleRow({{ $row->id }}, '{{ $relation }}')"
                        class="flex items-center gap-2 py-3 text-sm text-zinc-500"
                    >
                        <flux:icon.arrow-path class="size-4 animate-spin" />
                        <flux:text>{{ __('Loading data...') }}</flux:text>
                    </div>

                    <div wire:loading.remove wire:target="toggleRow({{ $row->id }}, '{{ $relation }}')">
                        @if($children->isEmpty())
                            <flux:text class="italic text-zinc-400">{{ __('No data available.') }}</flux:text>
                        @else
                            <flux:table>
                                <flux:table.columns>
                                    @foreach($childColumns as $childCol)
                                        <flux:table.column  :align="$childCol['align'] ?? null">{{ $childCol['label'] }}</flux:table.column>
                                    @endforeach
                                </flux:table.columns>
                                <flux:table.rows>
                                    @foreach($children as $child)
                                        <flux:table.row :key="'child-' . $child->id">
                                            @foreach($childColumns as $childCol)
                                                @php
                                                    if (!empty($childCol['relation'])) {
                                                        $value = data_get($child, str_replace(':', '.', $childCol['relation']));
                                                    } else {
                                                        $value = data_get($child, $childCol['key']);
                                                    }
                                                @endphp
                                                <flux:table.cell  :align="$childCol['align'] ?? null" :class="$childCol['cellClass'] ?? null" :variant="$childCol['variant']">{{ $value }}</flux:table.cell>
                                            @endforeach
                                        </flux:table.row>
                                    @endforeach
                                </flux:table.rows>
                            </flux:table>
                        @endif
                    </div>
                </div>
            </flux:table.cell>
        </flux:table.row>
    @endif
@endforeach