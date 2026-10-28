<?php

namespace KatakNyulink\LivewireDatatable\Support;

use Closure;

class Action
{
    public string $name;
    public ?string $label = null;
    public ?string $icon = null;
    public string $color = 'zinc';        // zinc|primary|danger|warning|success|info
    public string $variant = 'ghost';     // ghost|filled|outline|subtle
    public bool $requiresConfirmation = false;
    public string|Closure|null $confirmationTitle = null;
    public string|Closure|null $confirmationText = null;
    public string|Closure|null $confirmationButton = null;
    public ?Closure $action = null;
    public ?Closure $url = null;
    public ?Closure $visible = null;
    public ?Closure $disabled = null;
    public bool $openUrlInNewTab = false;
    public array $meta = [];

    public static function make(string $name): static
    {
        $a = new static();
        $a->name = $name;
        $a->label = str($name)->headline()->toString();
        return $a;
    }

    public function label(string $label): static
    {
        $this->label = $label;
        return $this;
    }
    public function icon(string $icon): static
    {
        $this->icon = $icon;
        return $this;
    }
    public function color(string $color): static
    {
        $this->color = $color;
        return $this;
    }
    public function variant(string $variant): static
    {
        $this->variant = $variant;
        return $this;
    }

    public function requiresConfirmation(bool $value = true): static
    {
        $this->requiresConfirmation = $value;
        return $this;
    }

    public function confirmationTitle(string|Closure $title): static
    {
        $this->confirmationTitle = $title;
        return $this;
    }
    public function confirmationText(string|Closure $text): static
    {
        $this->confirmationText = $text;
        return $this;
    }
    public function confirmationButton(string|Closure $label): static
    {
        $this->confirmationButton = $label;
        return $this;
    }

    public function action(Closure $callback): static
    {
        $this->action = $callback;
        return $this;
    }

    public function url(Closure $callback, bool $newTab = false): static
    {
        $this->url = $callback;
        $this->openUrlInNewTab = $newTab;
        return $this;
    }

    public function visible(Closure $callback): static
    {
        $this->visible = $callback;
        return $this;
    }
    public function disabled(Closure $callback): static
    {
        $this->disabled = $callback;
        return $this;
    }
    public function meta(array $meta): static
    {
        $this->meta = array_merge($this->meta, $meta);
        return $this;
    }

    public function isVisible($record): bool
    {
        return $this->visible ? (bool) ($this->visible)($record) : true;
    }

    public function isDisabled($record): bool
    {
        return $this->disabled ? (bool) ($this->disabled)($record) : false;
    }

    public function resolveUrl($record): ?string
    {
        return $this->url ? ($this->url)($record) : null;
    }

    public function execute($record): mixed
    {
        return $this->action ? ($this->action)($record) : null;
    }

    /** Resolve nilai dengan record (untuk closure) atau return string langsung */
    protected function resolveValue(mixed $value, mixed $record): mixed
    {
        if ($value instanceof Closure) {
            return $value($record);
        }
        return $value;
    }

    public function resolveConfirmationTitle(mixed $record): string
    {
        return (string) ($this->resolveValue($this->confirmationTitle, $record)
            ?? $this->label);
    }

    public function resolveConfirmationText(mixed $record): string
    {
        return (string) ($this->resolveValue($this->confirmationText, $record)
            ?? __('Are you sure you want to delete this record? This action cannot be undone.'));
    }

    public function resolveConfirmationButton(mixed $record): string
    {
        return (string) ($this->resolveValue($this->confirmationButton, $record)
            ?? __('Yes, Continue'));
    }

    public function toArray(): array
    {
        return [
            'name'                 => $this->name,
            'label'                => $this->label,
            'icon'                 => $this->icon,
            'color'                => $this->color,
            'variant'              => $this->variant,
            'requiresConfirmation' => $this->requiresConfirmation,
            'confirmationTitle'    => $this->confirmationTitle instanceof Closure ? '[closure]' : $this->confirmationTitle,
            'confirmationText'     => $this->confirmationText instanceof Closure ? '[closure]' : $this->confirmationText,
            'confirmationButton'   => $this->confirmationButton instanceof Closure ? '[closure]' : $this->confirmationButton,
            'openUrlInNewTab'      => $this->openUrlInNewTab,
            'meta'                 => $this->meta,
        ];
    }
}
