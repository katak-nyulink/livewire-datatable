<?php

namespace KatakNyulink\LivewireDatatable\Support;

class CompositeColumn
{
    public string $key;
    public string $label;
    public array $subFields = [];
    public string $layout = 'stack'; // stack|inline
    public string $separator = ' ';
    public bool $sortable = false;
    public bool $searchable = false;
    public bool $hideable = false;
    public ?string $align = null;
    public ?string $cellClass = null;
    public ?string $headClass = null;
    public ?string $width = null;
    public ?string $variant = null;

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
    public function sortable(bool $v = true): static
    {
        $this->sortable = $v;
        return $this;
    }
    public function searchable(bool $v = true): static
    {
        $this->searchable = $v;
        return $this;
    }
    public function hideable(bool $v = true): static
    {
        $this->hideable = $v;
        return $this;
    }

    public function align(string $align): static
    {
        $this->align = match ($align) {
            'left'   => 'start',
            'right'  => 'end',
            'center' => 'center',
            default  => $align,
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

    public function width(string $w): static
    {
        $this->width = $w;
        return $this;
    }

    public function variant(string $variant): static
    {
        $this->variant = $variant;
        return $this;
    }
    public function layout(string $layout): static
    {
        $this->layout = $layout; // stack|inline
        return $this;
    }

    public function separator(string $sep): static
    {
        $this->separator = $sep;
        return $this;
    }

    /**
     * Tambah sub-field.
     *   ->field('name', 'Nama')
     *   ->field('description', 'Deskripsi', fn($v) => Str::limit($v, 50))
     */
    public function field(string $key, ?string $label = null, ?\Closure $format = null, array $attrs = []): static
    {
        $this->subFields[] = [
            'key'    => $key,
            'label'  => $label,
            'format' => $format,
            'attrs'  => $attrs,
        ];
        return $this;
    }

    public function resolveValue($row): string
    {
        $parts = [];

        foreach ($this->subFields as $field) {
            $value = data_get($row, $field['key']);

            if ($field['format']) {
                $value = ($field['format'])($value, $row);
            }

            $parts[] = $value;
        }

        if ($this->layout === 'inline') {
            $escaped = array_map(fn($p) => e($p), $parts);
            return implode(e($this->separator), $escaped);
        }

        // Stack layout
        $html = '<div class="flex flex-col leading-tight">';
        foreach ($parts as $i => $part) {
            $size = $i === 0 ? 'text-sm font-medium' : 'text-xs text-zinc-500 dark:text-zinc-400';
            $html .= '<span class="' . $size . '">' . e($part) . '</span>';
        }
        $html .= '</div>';

        return $html;
    }

    public function toArray(): array
    {
        return [
            'key'        => $this->key,
            'label'      => $this->label,
            'sortable'   => $this->sortable,
            'searchable' => $this->searchable,
            'hideable'   => $this->hideable,
            'align'     => $this->align,        // 'start'|'center'|'end'
            'cellClass' => $this->cellClass,
            'headClass' => $this->headClass,
            'width'      => $this->width,
            'composite'  => true,
            'variant'   => $this->variant, // ← tambah
        ];
    }
}
