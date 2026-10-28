# MyDatatable

Modular advanced datatable untuk Laravel Livewire dengan Flux UI.

## Fitur

- ✅ **Livewire 4** + **Flux UI 2** (native `<flux:table>`)
- ✅ **Modular traits** — aktifkan hanya fitur yang dibutuhkan
- ✅ **Sorting, Search, Filter, Pagination**
- ✅ **Toggle columns** (show/hide dengan persistensi session)
- ✅ **Nested/expandable rows** (lazy load)
- ✅ **Record actions** (Filament-style `Action::make()`)
- ✅ **Dynamic confirmation** (title/text/button berdasarkan record)
- ✅ **Composite cells** (multiple fields dalam 1 kolom)
- ✅ **Cell variant** (strong/subtle)
- ✅ **Column width** (support CSS + Tailwind class)
- ✅ **Relasi Eloquent** (`supplier:name`)

## Instalasi

```bash
composer require kataknyulink/livewire-datatable
```

Publish config & views (opsional):

```bash
php artisan vendor:publish --tag=datatable-config
php artisan vendor:publish --tag=datatable-views
```

## Penggunaan

### 1. Buat Livewire Component

```php
namespace App\Livewire;

use App\Models\Category;
use KatakNyulink\LivewireDatabale\Livewire\NestedDataTable;
use KatakNyulink\LivewireDatabale\Support\Column;
use KatakNyulink\LivewireDatabale\Support\NestedRelation;

class CategoryTable extends NestedDataTable
{
    public string $model = Category::class;
    public array $relations = ['items.supplier'];

    protected function columns(): array
    {
        return [
            Column::make('id')->label('ID')->sortable()->width('60px'),
            Column::make('name')->label('Kategori')->sortable()->searchable(),
            Column::make('created_at')
                ->label('Dibuat')
                ->hideable()
                ->format(fn ($row) => $row->created_at->diffForHumans()),
        ];
    }

    protected function nestedRelations(): array
    {
        return [
            NestedRelation::make('items')
                ->label('Daftar Barang')
                ->orderBy('name', 'asc')
                ->addColumn('sku', fn ($c) => $c->label('SKU'))
                ->addColumn('name', fn ($c) => $c->label('Nama Barang')->searchable())
                ->addColumn('price', fn ($c) => $c
                    ->label('Harga')
                    ->align('right')
                    ->format(fn ($row) => 'Rp ' . number_format($row->price, 0, ',', '.')))
                ->addColumn(Column::make('supplier_name')
                    ->label('Supplier')
                    ->relation('supplier:name')),
        ];
    }
}
```

### 2. Render

```blade
<livewire:category-table />
```

## Modular Traits

Ingin hanya beberapa fitur? Extend `DataTable` dan pilih trait:

```php
use KatakNyulink\LivewireDatabale\Traits\HasColumns;
use KatakNyulink\LivewireDatabale\Traits\HasSorting;
use KatakNyulink\LivewireDatabale\Traits\HasSearch;

class SimpleTable extends \Livewire\Component
{
    use HasColumns, HasSorting, HasSearch;
    // ...
}
```

## License

MIT