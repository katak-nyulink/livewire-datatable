<?php

namespace KatakNyulink\LivewireDatatable\Support;

use Closure;

class Column
{
    public string $key;
    public string $label;
    public bool $sortable = false;
    public bool $searchable = false;
    public bool $hideable = false;
    public bool $visible = true;
    public ?string $relation = null;
    public ?Closure $format = null;
    public ?string $align = null; // start|center|end|null
    public ?string $cellClass = null;     // custom class tetap didukung
    public ?string $headClass = null;
    public ?string $width = null;
    public array $meta = [];
    public ?string $view = null;
    public ?string $variant = null; // strong|default|null
    public bool $sticky = false;
    public ?Closure $displayUsing = null;


    protected function __construct(string $key)
    {
        $this->key = $key;
        $this->label = str($key)->headline()->toString();
    }

    public static function make(string $key): static
    {
        return new static($key);
    }

    public function label(string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function sortable(bool $value = true): static
    {
        $this->sortable = $value;
        return $this;
    }

    public function searchable(bool $value = true): static
    {
        $this->searchable = $value;
        return $this;
    }

    public function hideable(bool $value = true): static
    {
        $this->hideable = $value;
        return $this;
    }

    public function hidden(bool $value = true): static
    {
        $this->visible = ! $value;
        $this->hideable = true;
        return $this;
    }

    public function relation(string $relation): static
    {
        $this->relation = $relation;
        return $this;
    }

    public function format(Closure $callback): static
    {
        $this->format = $callback;
        return $this;
    }

    public function align(string $align): static
    {
        // Map backward compat
        $this->align = match ($align) {
            'left'   => 'start',
            'right'  => 'end',
            'center' => 'center',
            default  => $align,   // start|end|center
        };
        return $this;
    }

    public function class(string $class): static
    {
        $this->cellClass = $class;
        return $this;
    }

    public function headClass(string $class): static
    {
        $this->headClass = $class;
        return $this;
    }

    public function width(string $width): static
    {
        $this->width = $width;
        return $this;
    }

    public function alignEnd(): static
    {
        $this->align = 'end';
        return $this;
    }

    public function alignCenter(): static
    {
        $this->align = 'center';
        return $this;
    }

    public function alignStart(): static
    {
        $this->align = 'start';
        return $this;
    }

    public function muted(): static
    {
        $this->cellClass = trim(($this->cellClass ?? '') . ' text-zinc-500 dark:text-zinc-400');
        return $this;
    }

    public function mono(): static
    {
        $this->cellClass = trim(($this->cellClass ?? '') . ' font-mono tabular-nums');
        return $this;
    }

    public function meta(array $meta): static
    {
        $this->meta = array_merge($this->meta, $meta);
        return $this;
    }

    public function variant(string $variant): static
    {
        $this->variant = $variant;
        return $this;
    }

    public function widthStyle(): ?string
    {
        if (! $this->width) return null;

        // Kalau style value (angka + unit), pakai style
        if (preg_match('/^\d+(\.\d+)?(px|%|rem|em|vw|vh)$/', $this->width)) {
            return "width: {$this->width}";
        }

        return null;
    }

    public function widthClass(): ?string
    {
        if (! $this->width) return null;

        // Kalau Tailwind class (w-*, min-w-*, max-w-*), pakai class
        if (preg_match('/^(w-|min-w-|max-w-)/', $this->width)) {
            return $this->width;
        }

        return null;
    }

    public function sticky(bool $value = true): static
    {
        $this->sticky = $value;
        return $this;
    }

    /**
     * Background class untuk sticky column.
     * Flux requires explicit background to prevent overlap.
     */
    public function stickyBackground(): ?string
    {
        if (! $this->sticky) return null;

        // Jika user sudah set background di cellClass/headClass, jangan override
        $existing = ($this->cellClass ?? '') . ' ' . ($this->headClass ?? '');
        if (preg_match('/\bbg-/', $existing)) {
            return null;
        }

        return 'bg-white dark:bg-zinc-900';
    }

    /** Gabungkan class user + background default sticky */
    public function resolvedCellClass(): ?string
    {
        return trim(($this->cellClass ?? '') . ' ' . ($this->stickyBackground() ?? '') . ' ' . ($this->widthClass() ?? ''));
    }

    public function resolvedHeadClass(): ?string
    {
        return trim(($this->headClass ?? '') . ' ' . ($this->stickyBackground() ?? '') . ' ' . ($this->widthClass() ?? ''));
    }

    public function resolveValue($row)
    {
        // Prioritas: view blade
        if ($this->view) {
            return view($this->view, [
                'row'    => $row,
                'column' => $this,
                'value'  => data_get($row, $this->key),
            ])->render();
        }

        // Prioritas: closure displayUsing
        if ($this->displayUsing) {
            return ($this->displayUsing)($row);
        }

        // Prioritas: closure format lama
        if ($this->format) {
            return ($this->format)($row);
        }

        // Fallback: relasi atau key biasa
        if ($this->relation) {
            return data_get($row, str_replace(':', '.', $this->relation));
        }

        return data_get($row, $this->key);
    }

    public function toArray(): array
    {
        return [
            'key'        => $this->key,
            'label'      => $this->label,
            'sortable'   => $this->sortable,
            'searchable' => $this->searchable,
            'hideable'   => $this->hideable,
            'visible'    => $this->visible,
            'relation'   => $this->relation,
            'align'     => $this->align,        // 'start'|'center'|'end'
            'cellClass' => $this->cellClass,
            'headClass' => $this->headClass,
            'width'      => $this->width,
            'meta'       => $this->meta,
            'variant'    => $this->variant,
            'sticky'     => $this->sticky,
            'composite'   => false,
        ];
    }

    public function view(string $view): static
    {
        $this->view = $view;
        return $this;
    }

    public function displayUsing(Closure $callback): static
    {
        $this->displayUsing = $callback;
        return $this;
    }
}
