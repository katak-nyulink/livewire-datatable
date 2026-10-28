<?php

use Livewire\Livewire;
use KatakNyulink\LivewireDatatable\Livewire\NestedDataTable;
use KatakNyulink\LivewireDatatable\Support\Column;
use KatakNyulink\LivewireDatatable\Support\NestedRelation;
use KatakNyulink\LivewireDatatable\Tests\Fixtures\Category;
use KatakNyulink\LivewireDatatable\Tests\Fixtures\Item;
use KatakNyulink\LivewireDatatable\Tests\Fixtures\Supplier;

class TestCategoryTable extends NestedDataTable
{
    public string $model = Category::class;

    protected function columns(): array
    {
        return [
            Column::make('id')->label('ID')->sortable(),
            Column::make('name')->label('Kategori')->sortable()->searchable(),
            Column::make('created_at')->label('Dibuat')->hideable(),
        ];
    }

    protected function nestedRelations(): array
    {
        return [
            NestedRelation::make('items')
                ->label('Daftar Barang')
                ->addColumn('sku', fn ($c) => $c->label('SKU'))
                ->addColumn('name', fn ($c) => $c->label('Nama'))
                ->addColumn('price', fn ($c) => $c->label('Harga')->align('right'))
                ->addColumn(Column::make('supplier_name')
                    ->label('Supplier')
                    ->relation('supplier:name')),
        ];
    }
}

it('renders nested datatable', function () {
    $cat = Category::create(['name' => 'Elektronik']);
    $supplier = Supplier::create(['name' => 'PT Maju']);
    Item::create([
        'category_id' => $cat->id,
        'supplier_id' => $supplier->id,
        'sku' => 'ELC-001',
        'name' => 'Laptop',
        'price' => 12500000,
    ]);

    Livewire::test(TestCategoryTable::class)
        ->assertSee('Elektronik')
        ->call('toggleRow', $cat->id, 'items')
        ->assertSee('Laptop')
        ->assertSee('PT Maju');
});

it('filters by search term', function () {
    Category::create(['name' => 'Elektronik']);
    Category::create(['name' => 'Pakaian']);

    Livewire::test(TestCategoryTable::class)
        ->set('search', 'Elektronik')
        ->assertSee('Elektronik')
        ->assertDontSee('Pakaian');
});

it('toggles column visibility', function () {
    Category::create(['name' => 'Test']);

    $component = Livewire::test(TestCategoryTable::class)
        ->assertSee('Dibuat');

    $component->call('toggleColumn', 'created_at')
        ->assertDontSee('Dibuat');
});