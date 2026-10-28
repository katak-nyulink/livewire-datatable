<?php

namespace KatakNyulink\LivewireDatatable\Traits;

use Livewire\Attributes\Computed;
use KatakNyulink\LivewireDatatable\Support\Action;

trait HasRecordActions
{
    /** @var Action[] — protected, tidak diserialisasi */
    protected array $actionDefinitions = [];

    public ?string $pendingActionName = null;
    public mixed $pendingActionRecordId = null;
    public bool $showConfirmModal = false;

    protected function actions(): array
    {
        return [];
    }

    protected function bootActions(): void
    {
        $this->actionDefinitions = [];

        if (method_exists($this, 'actions')) {
            $this->actionDefinitions = $this->actions();
        }
    }

    #[Computed]
    public function hasRecordActions(): bool
    {
        return ! empty($this->actionDefinitions);
    }

    #[Computed]
    public function recordActions(): array
    {
        return $this->actionDefinitions;
    }

    #[Computed]
    public function pendingAction(): ?Action
    {
        if (! $this->pendingActionName) return null;
        return collect($this->actionDefinitions)->firstWhere('name', $this->pendingActionName);
    }

    public function callAction(string $name, mixed $recordId): void
    {
        $action = collect($this->actionDefinitions)->firstWhere('name', $name);
        if (! $action) {
            \Illuminate\Support\Facades\Log::warning("Action [{$name}] tidak ditemukan", [
                'available' => collect($this->actionDefinitions)->pluck('name')->toArray(),
            ]);
            return;
        }

        if ($action->requiresConfirmation) {
            $this->pendingActionName = $name;
            $this->pendingActionRecordId = $recordId;
            $this->showConfirmModal = true;
            return;
        }

        $this->executeAction($name, $recordId);
    }

    public function confirmPendingAction(): void
    {
        $name   = $this->pendingActionName;
        $record = $this->pendingActionRecordId;

        if ($name && $record !== null) {
            $this->executeAction($name, $record);
        }
        $this->closePendingAction();
    }

    public function closePendingAction(): void
    {
        $this->pendingActionName = null;
        $this->pendingActionRecordId = null;
        $this->showConfirmModal = false;
    }

    protected function executeAction(string $name, mixed $recordId): void
    {
        $action = collect($this->actionDefinitions)->firstWhere('name', $name);
        if (! $action || ! $action->action) return;

        $record = $this->model::find($recordId);
        if (! $record) return;

        $action->execute($record);

        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }
    }

    #[Computed]
    public function pendingActionRecord(): mixed
    {
        if ($this->pendingActionRecordId === null) return null;
        return $this->model::find($this->pendingActionRecordId);
    }

    #[Computed]
    public function pendingActionTitle(): ?string
    {
        // Panggil sebagai METHOD, bukan property
        $action = $this->pendingAction();
        $record = $this->pendingActionRecord();

        if (! $action || ! $record) return null;
        return $action->resolveConfirmationTitle($record);
    }

    #[Computed]
    public function pendingActionText(): ?string
    {
        $action = $this->pendingAction();
        $record = $this->pendingActionRecord();

        if (! $action || ! $record) return null;
        return $action->resolveConfirmationText($record);
    }

    #[Computed]
    public function pendingActionButton(): ?string
    {
        $action = $this->pendingAction();
        $record = $this->pendingActionRecord();

        if (! $action || ! $record) return null;
        return $action->resolveConfirmationButton($record);
    }
}
