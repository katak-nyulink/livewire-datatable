<flux:modal wire:model="showConfirmModal">
    @if($this->pendingAction)
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ $this->pendingActionTitle }}
                </flux:heading>
                <flux:text class="mt-2 whitespace-pre-line">
                    {{ $this->pendingActionText }}
                </flux:text>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="closePendingAction">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button
                    variant="{{ $this->pendingAction->color === 'danger' ? 'danger' : 'primary' }}"
                    wire:click="confirmPendingAction"
                >
                    {{ $this->pendingActionButton }}
                </flux:button>
            </div>
        </div>
    @endif
</flux:modal>