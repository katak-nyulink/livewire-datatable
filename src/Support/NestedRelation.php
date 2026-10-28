<?php

namespace KatakNyulink\LivewireDatatable\Support;

use Closure;

class NestedRelation
{
    public string $name;
    public string $label;
    public array $columns = [];
    public ?string $orderBy = null;
    public string $orderDir = 'asc';
    public array $with = [];
    public ?Closure $queryCallback = null;
    public array $nested = [];

    protected function __construct(string $name)
    {
        $this->name = $name;
        $this->label = str($name)->headline()->toString();
    }

    public static function make(string $name): static
    {
        return new static($name);
    }

    public function label(string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function columns(array $columns): static
    {
        $this->columns = $columns;
        return $this;
    }

    public function addColumn(Column|string $column, ?callable $callback = null): static
    {
        if (is_string($column)) {
            $col = Column::make($column);
            if ($callback) {
                $callback($col);
            }
            $this->columns[] = $col;
        } else {
            $this->columns[] = $column;
        }
        return $this;
    }

    public function addNested(NestedRelation $nested): static
    {
        $this->nested[] = $nested;
        return $this;
    }

    public function orderBy(string $column, string $direction = 'asc'): static
    {
        $this->orderBy = $column;
        $this->orderDir = $direction;
        return $this;
    }

    public function with(array $relations): static
    {
        $this->with = $relations;
        return $this;
    }

    public function query(Closure $callback): static
    {
        $this->queryCallback = $callback;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'label'   => $this->label,
            'columns' => array_map(
                fn ($c) => $c instanceof Column ? $c->toArray() : $c,
                $this->columns
            ),
            'nested'  => array_map(fn ($n) => $n->toArray(), $this->nested),
            'orderBy' => $this->orderBy ? [$this->orderBy, $this->orderDir] : null,
            'with'    => $this->with,
        ];
    }
}