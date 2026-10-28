<?php

namespace KatakNyulink\LivewireDatatable\Support;

use Illuminate\Support\Facades\Blade;

class Cell
{
    /** Render komponen Blade dari string */
    public static function render(string $template): string
    {
        return Blade::render($template);
    }

    /** Render komponen Flux badge dengan aman */
    public static function badge(string $text, string $variant = 'solid', string $color = 'zinc', string $size = 'sm',  string $class = ""): string
    {
        return Blade::render(
            '<flux:badge variant="{{ $variant }}" color="{{ $color }}" size="{{ $size }}" class="{{ $class }}" inset="top bottom">{{ $text }}</flux:badge>',
            ['variant' => $variant, 'color' => $color, 'size' => $size, 'text' => $text, 'class' => $class]
        );
    }
}
