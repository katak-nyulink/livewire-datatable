<?php

namespace KatakNyulink\LivewireDatatable\Livewire;

use KatakNyulink\LivewireDatatable\Support\NestedRelation;
use KatakNyulink\LivewireDatatable\Traits\HasNestedRows;

abstract class NestedDataTable extends Datatable
{
    use HasNestedRows;

    /** @var NestedRelation[] */
    protected array $nestedRelationObjects = [];

    protected function nestedRelations(): array
    {
        return [];
    }

    public function addNestedRelation(NestedRelation|string $relation, ?callable $callback = null): static
    {
        if (is_string($relation)) {
            $obj = NestedRelation::make($relation);
            if ($callback) {
                $callback($obj);
            }
            $this->nestedRelationObjects[] = $obj;
        } else {
            $this->nestedRelationObjects[] = $relation;
        }
        return $this;
    }

    protected function bootNestedRows(): void
    {
        $this->nestedRelationObjects = $this->nestedRelations();
        $this->nestedRelations = [];
        $this->nestedConfig = [];
        $this->nestedColumns = [];

        foreach ($this->nestedRelationObjects as $rel) {
            $this->registerNested($rel->name, $rel->toArray());
        }

        $this->initNestedRows();
    }

    protected function registerNestedAll(): void
    {
        $this->nestedRelations = [];
        $this->nestedConfig = [];
        $this->nestedColumns = [];

        foreach ($this->nestedRelationObjects as $rel) {
            $this->registerNested($rel->name, $rel->toArray());
        }

        $this->initNestedRows();
    }

    /**
     * Override: tambah re-init nested setelah parent init.
     */
    protected function initializeDataTable(): void
    {
        parent::initializeDataTable();

        // Rebuild nested state
        $this->nestedRelationObjects = array_merge(
            $this->nestedRelationObjects,
            $this->nestedRelations()
        );

        $this->registerNestedAll();
    }

    public function mount(?string $model = null, array $relations = []): void
    {
        parent::mount($model, $relations);

        $this->nestedRelationObjects = array_merge(
            $this->nestedRelationObjects,
            $this->nestedRelations()
        );

        foreach ($this->nestedRelationObjects as $rel) {
            $this->registerNested($rel->name, $rel->toArray());
        }

        $this->initNestedRows();
    }

    public function render()
    {
        return view('datatable::livewire.nested-datatable', [
            'data' => $this->query()->paginate($this->perPage),
        ]);
    }
}
