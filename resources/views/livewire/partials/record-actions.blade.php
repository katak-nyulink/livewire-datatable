@php
    $limit   = config('datatable.inline_actions_limit', 2);
    $actions = collect($this->recordActions)->filter(fn($a) => $a->isVisible($row));
    $inline  = $actions->take($limit);
    $more    = $actions->skip($limit);
@endphp

<div class="flex items-center justify-end gap-1">
    @foreach($inline as $action)
        @php
            $url        = $action->resolveUrl($row);
            $isDisabled = $action->isDisabled($row);
        @endphp

        @if($url)
            <flux:button
                :href="$url"
                :target="$action->openUrlInNewTab ? '_blank' : null"
                size="sm"
                variant="{{ $action->variant }}"
                :icon="$action->icon"
                :disabled="$isDisabled"
                :title="$action->label"
            />
        @else
            <flux:button
                size="sm"
                variant="{{ $action->variant }}"
                :icon="$action->icon"
                :disabled="$isDisabled"
                :title="$action->label"
                wire:click="callAction('{{ $action->name }}', {{ $row->id }})"
                wire:loading.attr="disabled"
                wire:target="callAction('{{ $action->name }}', {{ $row->id }})"
            />
        @endif
    @endforeach

    @if($more->isNotEmpty())
        <flux:dropdown align="end">
            <flux:button size="sm" variant="ghost" icon="ellipsis-horizontal" />

            <flux:menu>
                @foreach($more as $action)
                    @php
                        $url        = $action->resolveUrl($row);
                        $isDisabled = $action->isDisabled($row);
                        $isDanger   = $action->color === 'danger';
                    @endphp

                    @if($url)
                        <flux:menu.item
                            :href="$url"
                            :target="$action->openUrlInNewTab ? '_blank' : null"
                            :icon="$action->icon"
                            :disabled="$isDisabled"
                        >
                            {{ $action->label }}
                        </flux:menu.item>
                    @else
                        <flux:menu.item
                            :icon="$action->icon"
                            :disabled="$isDisabled"
                            :variant="$isDanger ? 'danger' : null"
                            wire:click="callAction('{{ $action->name }}', {{ $row->id }})"
                        >
                            {{ $action->label }}
                        </flux:menu.item>
                    @endif
                @endforeach
            </flux:menu>
        </flux:dropdown>
    @endif
</div>