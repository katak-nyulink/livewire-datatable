<?php

return [
    'default_per_page' => 10,

    'per_page_options' => [5, 10, 25, 50, 100],

    'session_key' => 'datatable',

    'persist_state' => true,

    'icons' => [
        'sort_asc'       => 'chevron-up',
        'sort_desc'      => 'chevron-down',
        'expand'         => 'chevron-right',
        'search'         => 'magnifying-glass',
        'filter'         => 'funnel',
        'columns'        => 'view-columns',
        'loading'        => 'arrow-path',
    ],

    'classes' => [
        'wrapper'        => 'w-full',
        'table'          => 'min-w-full',
        'row_expanded'   => 'bg-zinc-50 dark:bg-zinc-900/60',
        'nested_wrapper' => 'pl-12 py-4',
        'nested_label'   => 'text-xs font-semibold uppercase tracking-wider text-zinc-500 mb-2',
        'nested_table'   => 'rounded-lg border border-zinc-200 dark:border-zinc-800 overflow-hidden',
    ],

    'sticky_header' => true,               // aktifkan sticky header
    'sticky_header_max_height' => '600px',  // atau class Tailwind seperti 'max-h-[600px]'
    'sticky_column' => false,               // aktifkan sticky column
    'sticky_column_max_width' => '300px',  // atau class Tailwind seperti 'max-w-[300px]'
];
